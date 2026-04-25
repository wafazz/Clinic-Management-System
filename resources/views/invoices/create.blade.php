<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Create Invoice</h4></x-slot>

    <div class="card"><div class="card-body">
            @if(!empty($selectedConsultation))
                <div class="alert alert-info py-2">
                    <i class="mdi mdi-stethoscope mr-1"></i>
                    Creating invoice for consultation <strong>{{ $selectedConsultation->consultation_number }}</strong>
                    — Patient: <strong>{{ $selectedConsultation->patient->name }}</strong>,
                    Doctor: Dr. {{ $selectedConsultation->doctor->user->name }}
                    @if($selectedConsultation->doctor->consultation_fee)
                        <span class="ml-2">| Consultation Fee: <strong>RM {{ number_format($selectedConsultation->doctor->consultation_fee, 2) }}</strong></span>
                    @endif
                    @if(!empty($prefillItems) && count($prefillItems) > 1)
                        <br><small><i class="mdi mdi-check"></i> Auto pre-filled <strong>{{ count($prefillItems) }} line items</strong> (consultation fee + dispensed Rx + lab tests)</small>
                    @endif
                </div>
            @endif
            @if(!empty($membership))
                <div class="alert alert-success py-2">
                    <i class="mdi mdi-card-account-details mr-1"></i>
                    Patient is <strong>{{ $membership->tier->name }}</strong> member ({{ $membership->membership_number }}) —
                    Discounts: Cons {{ $membership->tier->discount_consultation }}% / Med {{ $membership->tier->discount_medicine }}% / Lab {{ $membership->tier->discount_lab }}%.
                    Tick "Apply Membership Discount" below.
                </div>
            @endif
            <form method="POST" action="{{ route('invoices.store') }}"  x-data="invoiceForm()">
                @csrf
                @if(!empty($selectedConsultation))
                    <input type="hidden" name="consultation_id" value="{{ $selectedConsultation->id }}">
                @endif
                <div class="row">
                    <div>
                        <label class="form-label">Patient *</label>
                        <select name="patient_id" required class="form-control" x-on:change="patientId = $event.target.value">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatient ?? null) == $patient->id ? 'selected' : '' }}>{{ $patient->patient_id }} - {{ $patient->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Appointment (optional)</label>
                        <select name="appointment_id" class="form-control">
                            <option value="">None</option>
                            @foreach($appointments as $appt)
                                <option value="{{ $appt->id }}" {{ old('appointment_id', $selectedAppointment) == $appt->id ? 'selected' : '' }}>{{ $appt->appointment_date->format('d/m/Y') }} - {{ $appt->patient->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Payment Type --}}
                <div class="row p-3 bg-light rounded mb-3">
                    <div>
                        <label class="form-label">Payment Type *</label>
                        <select name="payment_type" x-model="paymentType" class="form-control">
                            <option value="cash">Cash / Self-pay</option>
                            <option value="panel">Panel / Insurance</option>
                        </select>
                    </div>
                    <div x-show="paymentType === 'panel'">
                        <label class="form-label">Insurance Panel *</label>
                        <select name="insurance_panel_id" x-bind:required="paymentType === 'panel'" class="form-control">
                            <option value="">Select Panel</option>
                            @foreach($insurancePanels as $panel)
                                <option value="{{ $panel->id }}" {{ old('insurance_panel_id') == $panel->id ? 'selected' : '' }}>{{ $panel->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="paymentType === 'panel'">
                        <label class="form-label">Patient Insurance</label>
                        <select name="patient_insurance_id" class="form-control">
                            <option value="">Select (optional)</option>
                            @foreach($patientInsurances as $pi)
                                <option value="{{ $pi->id }}" {{ old('patient_insurance_id') == $pi->id ? 'selected' : '' }}>{{ $pi->panel->company_name ?? '' }} - {{ $pi->member_id ?? $pi->policy_number ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h3 class="text-lg font-weight-bold border-b pb-2">Line Items</h3>
                <template x-for="(item, index) in items" :key="index">
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <label x-show="index === 0" class="form-label">Kind</label>
                            <select :name="'items['+index+'][kind]'" x-model="item.kind" class="form-control form-control-sm">
                                <option value="custom">Custom</option>
                                <option value="consultation">Consultation</option>
                                <option value="medicine">Medicine</option>
                                <option value="lab">Lab</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label x-show="index === 0" class="form-label">Service</label>
                            <select :name="'items['+index+'][service_id]'" x-on:change="prefillService($event, index)" class="form-control form-control-sm">
                                <option value="">-</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}" data-name="{{ $svc->name }}" data-price="{{ $svc->price }}">{{ $svc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label x-show="index === 0" class="form-label">Description *</label>
                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" required class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-1">
                            <label x-show="index === 0" class="form-label">Qty</label>
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" min="1" required class="form-control form-control-sm" @input="calcTotal" />
                        </div>
                        <div class="col-md-2">
                            <label x-show="index === 0" class="form-label">Price (RM)</label>
                            <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model.number="item.unit_price" min="0" required class="form-control form-control-sm" @input="calcTotal" />
                        </div>
                        <div class="col-md-1 text-sm font-medium pt-4" x-text="'RM ' + (item.quantity * item.unit_price).toFixed(2)"></div>
                        <div class="col-md-1 pt-4">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-danger text-sm">X</button>
                        </div>
                    </div>
                </template>
                <button type="button" @click="addItem()" class="text-primary text-sm font-medium">+ Add Item</button>

                <div class="row border-top pt-3">
                    <div>
                        <label class="form-label">Tax (RM)</label>
                        <input type="number" step="0.01" name="tax" x-model.number="tax" value="0" class="form-control" @input="calcTotal" />
                    </div>
                    <div>
                        <label class="form-label">Discount (RM)</label>
                        <input type="number" step="0.01" name="discount" x-model.number="discount" value="0" class="form-control" @input="calcTotal" />
                    </div>
                    @if(!empty($membership))
                    <div>
                        <label class="form-label d-block">Membership</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="apply_membership_discount" value="1" id="ams" x-model="applyMembership" class="form-check-input" @change="calcTotal" checked>
                            <label for="ams" class="form-check-label">Apply Discount</label>
                        </div>
                        <small class="text-success" x-show="applyMembership && membershipDiscount > 0">- RM <span x-text="membershipDiscount.toFixed(2)"></span></small>
                    </div>
                    @endif
                    <div class="d-flex align-items-end">
                        <div class="h5 font-bold">Total: RM <span x-text="grandTotal.toFixed(2)">0.00</span></div>
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Create Invoice</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function invoiceForm() {
            @php
                $defaultItems = [];
                if (!empty($prefillItems)) {
                    foreach ($prefillItems as $i => $item) {
                        // Determine kind from description
                        $kind = 'custom';
                        if (str_starts_with($item['description'], 'Consultation')) $kind = 'consultation';
                        elseif (str_starts_with($item['description'], 'Lab:')) $kind = 'lab';
                        elseif (!str_starts_with($item['description'], 'Consultation') && !str_starts_with($item['description'], 'Lab:')) $kind = 'medicine';
                        $defaultItems[] = array_merge($item, ['kind' => $kind]);
                    }
                }
                if (empty($defaultItems)) {
                    $defaultItems = [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'kind' => 'custom']];
                }
                $tierDiscounts = !empty($membership) ? [
                    'consultation' => (float) $membership->tier->discount_consultation,
                    'medicine' => (float) $membership->tier->discount_medicine,
                    'lab' => (float) $membership->tier->discount_lab,
                ] : ['consultation' => 0, 'medicine' => 0, 'lab' => 0];
            @endphp
            return {
                items: @json($defaultItems),
                tax: 0,
                discount: 0,
                grandTotal: 0,
                applyMembership: {{ !empty($membership) ? 'true' : 'false' }},
                membershipDiscount: 0,
                tierRates: @json($tierDiscounts),
                paymentType: '{{ old('payment_type', 'cash') }}',
                patientId: '{{ old('patient_id') }}',
                init() { this.calcTotal(); },
                addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, kind: 'custom' }); },
                removeItem(index) { this.items.splice(index, 1); this.calcTotal(); },
                prefillService(e, index) {
                    let opt = e.target.selectedOptions[0];
                    if (opt.dataset.name) {
                        this.items[index].description = opt.dataset.name;
                        this.items[index].unit_price = parseFloat(opt.dataset.price) || 0;
                        this.items[index].kind = 'service';
                        this.calcTotal();
                    }
                },
                calcTotal() {
                    let sub = this.items.reduce((sum, i) => sum + (i.quantity * i.unit_price), 0);
                    this.membershipDiscount = 0;
                    if (this.applyMembership) {
                        for (let i of this.items) {
                            let rate = this.tierRates[i.kind] || 0;
                            this.membershipDiscount += (i.quantity * i.unit_price) * (rate / 100);
                        }
                    }
                    this.grandTotal = sub + (this.tax || 0) - (this.discount || 0) - this.membershipDiscount;
                }
            }
        }
    </script>
</x-app-layout>
