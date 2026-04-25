<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Create Insurance Claim</h4></x-slot>

    <div class="card"><div class="card-body">
            @if($invoices->isEmpty())
                <div class="text-center py-8">
                    <p class="text-muted mb-4">No panel invoices available for claiming. Create a panel invoice first.</p>
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">Create Invoice</a>
                </div>
            @else
                <form method="POST" action="{{ route('insurance-claims.store') }}"  x-data="claimForm()">
                    @csrf
                    <div>
                        <label class="form-label">Panel Invoice *</label>
                        <select name="invoice_id" required x-on:change="selectInvoice($event)" class="form-control">
                            <option value="">Select Invoice</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" data-total="{{ $inv->total }}" data-patient="{{ $inv->patient->name ?? '' }}" data-panel="{{ $inv->insurancePanel->company_name ?? '' }}" {{ $selectedInvoice == $inv->id ? 'selected' : '' }}>
                                    {{ $inv->invoice_number }} - {{ $inv->patient->name ?? '' }} (RM {{ number_format($inv->total, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('invoice_id') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="bg-light rounded-lg p-4" x-show="selectedPatient">
                        <p class="text-sm"><span class="text-muted">Patient:</span> <span x-text="selectedPatient" class="font-medium"></span></p>
                        <p class="text-sm"><span class="text-muted">Panel:</span> <span x-text="selectedPanel" class="font-medium"></span></p>
                        <p class="text-sm"><span class="text-muted">Invoice Total:</span> RM <span x-text="invoiceTotal" class="font-medium"></span></p>
                    </div>

                    <div class="row">
                        <div>
                            <label class="form-label">Claim Amount (RM) *</label>
                            <input type="number" step="0.01" name="claim_amount" x-model="claimAmount" required class="form-control" />
                        </div>
                        <div>
                            <label class="form-label">Patient Co-pay (RM)</label>
                            <input type="number" step="0.01" name="patient_copay" value="{{ old('patient_copay', 0) }}" class="form-control" />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">GL Number (if applicable)</label>
                        <input type="text" name="gl_number" value="{{ old('gl_number') }}" class="form-control" placeholder="e.g., GL-2026-001234" />
                    </div>
                    <div>
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary btn-sm">Create Claim</button>
                        <a href="{{ route('insurance-claims.index') }}" class="btn btn-light btn-sm">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script>
        function claimForm() {
            return {
                selectedPatient: '',
                selectedPanel: '',
                invoiceTotal: '0.00',
                claimAmount: 0,
                selectInvoice(e) {
                    let opt = e.target.selectedOptions[0];
                    this.selectedPatient = opt.dataset.patient || '';
                    this.selectedPanel = opt.dataset.panel || '';
                    this.invoiceTotal = parseFloat(opt.dataset.total || 0).toFixed(2);
                    this.claimAmount = parseFloat(opt.dataset.total || 0);
                }
            }
        }
    </script>
</x-app-layout>
