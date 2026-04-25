<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Claim: {{ $insuranceClaim->claim_number }}</h4>
            @php $statusColors = ['draft' => 'badge-secondary', 'submitted' => 'badge-info', 'approved' => 'badge-success', 'partial' => 'badge-warning', 'rejected' => 'badge-danger', 'paid' => 'badge-success']; @endphp
            <span class="badge {{ $statusColors[$insuranceClaim->status] ?? 'badge-secondary' }}">{{ ucfirst($insuranceClaim->status) }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 table-success text-success px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-light text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="row mb-4">
        {{-- Claim Details --}}
        <div class="card"><div class="card-body">
            <h3 class="card-title">Claim Details</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Claim Number</dt><dd class="font-medium">{{ $insuranceClaim->claim_number }}</dd></div>
                <div><dt class="text-muted">Patient</dt><dd><a href="{{ route('patients.show', $insuranceClaim->patient) }}" >{{ $insuranceClaim->patient->name }}</a></dd></div>
                <div><dt class="text-muted">Panel</dt><dd><a href="{{ route('insurance-panels.show', $insuranceClaim->panel) }}" >{{ $insuranceClaim->panel->company_name }}</a></dd></div>
                @if($insuranceClaim->patientInsurance)
                    <div><dt class="text-muted">Member ID</dt><dd>{{ $insuranceClaim->patientInsurance->member_id ?? '-' }}</dd></div>
                    <div><dt class="text-muted">Policy Number</dt><dd>{{ $insuranceClaim->patientInsurance->policy_number ?? '-' }}</dd></div>
                @endif
                <div><dt class="text-muted">Invoice</dt><dd><a href="{{ route('invoices.show', $insuranceClaim->invoice) }}" >{{ $insuranceClaim->invoice->invoice_number }}</a></dd></div>
            </dl>
        </div>

        {{-- Financial Details --}}
        <div class="card"><div class="card-body">
            <h3 class="card-title">Financial Details</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Invoice Total</dt><dd class="font-bold">RM {{ number_format($insuranceClaim->invoice->total, 2) }}</dd></div>
                <div><dt class="text-muted">Claim Amount</dt><dd class="font-bold text-primary">RM {{ number_format($insuranceClaim->claim_amount, 2) }}</dd></div>
                <div><dt class="text-muted">Approved Amount</dt><dd class="font-bold text-success">{{ $insuranceClaim->approved_amount ? 'RM ' . number_format($insuranceClaim->approved_amount, 2) : 'Pending' }}</dd></div>
                <div><dt class="text-muted">Patient Co-pay</dt><dd>RM {{ number_format($insuranceClaim->patient_copay, 2) }}</dd></div>

                @if($insuranceClaim->gl_number || $insuranceClaim->gl_status !== 'not_required')
                    <div class="border-t pt-2 mt-2">
                        <dt class="text-muted">GL Number</dt><dd>{{ $insuranceClaim->gl_number ?? 'Pending' }}</dd>
                    </div>
                    <div>
                        @php $glColors = ['not_required' => 'badge-secondary', 'pending' => 'badge-warning', 'approved' => 'badge-success', 'rejected' => 'badge-danger']; @endphp
                        <dt class="text-muted">GL Status</dt>
                        <dd><span class="badge {{ $glColors[$insuranceClaim->gl_status] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $insuranceClaim->gl_status)) }}</span></dd>
                    </div>
                @endif

                @if($insuranceClaim->submission_date)
                    <div><dt class="text-muted">Submitted</dt><dd>{{ $insuranceClaim->submission_date->format('d M Y') }}</dd></div>
                @endif
                @if($insuranceClaim->approval_date)
                    <div><dt class="text-muted">Approved</dt><dd>{{ $insuranceClaim->approval_date->format('d M Y') }}</dd></div>
                @endif
                @if($insuranceClaim->payment_date)
                    <div><dt class="text-muted">Paid</dt><dd>{{ $insuranceClaim->payment_date->format('d M Y') }}</dd></div>
                @endif
                @if($insuranceClaim->payment_reference)
                    <div><dt class="text-muted">Payment Ref</dt><dd>{{ $insuranceClaim->payment_reference }}</dd></div>
                @endif
                @if($insuranceClaim->rejection_reason)
                    <div class="border-t pt-2 mt-2"><dt class="text-muted text-danger">Rejection Reason</dt><dd class="text-danger">{{ $insuranceClaim->rejection_reason }}</dd></div>
                @endif
                @if($insuranceClaim->notes)
                    <div><dt class="text-muted">Notes</dt><dd>{{ $insuranceClaim->notes }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Invoice Items --}}
    <div class="card"><div class="card-body mb-6">
        <h3 class="card-title">Invoice Items</h3>
        <table class="table table-hover">
            <thead><tr>
                <th class="text-left py-2">Description</th>
                <th class="text-right py-2">Qty</th>
                <th class="text-right py-2">Price</th>
                <th class="text-right py-2">Total</th>
            </tr></thead>
            <tbody>
                @foreach($insuranceClaim->invoice->items as $item)
                    <tr class="border-t">
                        <td class="py-2">{{ $item->description }}</td>
                        <td class="py-2 text-right">{{ $item->quantity }}</td>
                        <td class="py-2 text-right">RM {{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2 text-right">RM {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="border-t font-bold">
                    <td colspan="3" class="py-2 text-right">Invoice Total:</td>
                    <td class="py-2 text-right">RM {{ number_format($insuranceClaim->invoice->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Update Status --}}
    @if(!in_array($insuranceClaim->status, ['paid']))
        <div class="card"><div class="card-body mb-6">
            <h3 class="card-title">Update Claim Status</h3>
            <form method="POST" action="{{ route('insurance-claims.update-status', $insuranceClaim) }}" >
                @csrf @method('PATCH')
                <div class="row">
                    <div>
                        <label class="form-label">New Status *</label>
                        <select name="status" required class="form-control" x-data x-on:change="
                            document.getElementById('approved-fields').style.display = ['approved','partial'].includes($event.target.value) ? 'block' : 'none';
                            document.getElementById('rejected-fields').style.display = $event.target.value === 'rejected' ? 'block' : 'none';
                            document.getElementById('paid-fields').style.display = $event.target.value === 'paid' ? 'block' : 'none';">
                            @foreach(['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved', 'partial' => 'Partial Approved', 'rejected' => 'Rejected', 'paid' => 'Paid'] as $val => $label)
                                <option value="{{ $val }}" {{ $insuranceClaim->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="approved-fields" style="display: none;">
                        <label class="form-label">Approved Amount (RM)</label>
                        <input type="number" step="0.01" name="approved_amount" value="{{ old('approved_amount', $insuranceClaim->approved_amount ?? $insuranceClaim->claim_amount) }}" class="form-control" />
                    </div>
                    <div id="rejected-fields" style="display: none;">
                        <label class="form-label">Rejection Reason</label>
                        <input type="text" name="rejection_reason" value="{{ old('rejection_reason') }}" class="form-control" />
                    </div>
                    <div id="paid-fields" style="display: none;">
                        <label class="form-label">Payment Reference</label>
                        <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
            </form>
        </div>
    @endif

    <a href="{{ route('insurance-claims.index') }}" class="btn btn-light btn-sm">Back to Claims</a>
</x-app-layout>
