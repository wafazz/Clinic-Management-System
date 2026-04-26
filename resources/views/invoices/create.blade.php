<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-receipt-text-outline text-primary mr-1"></i>Create Invoice</h4>
                <small class="text-muted">Bill a patient for services rendered</small>
            </div>
            <a href="{{ route('invoices.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Invoices</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @if(!empty($selectedConsultation))
        <div class="data-card mb-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border:none">
            <div class="d-flex align-items-center flex-wrap" style="gap:14px">
                <i class="mdi mdi-stethoscope" style="font-size:30px;opacity:0.9"></i>
                <div style="flex:1;min-width:200px">
                    <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">From Consultation</small>
                    <div class="font-weight-bold">{{ $selectedConsultation->consultation_number }}</div>
                    <div class="small" style="opacity:0.9">
                        {{ $selectedConsultation->patient->name }} · Dr. {{ $selectedConsultation->doctor->user->name }}
                        @if($selectedConsultation->doctor->consultation_fee) · Fee RM {{ number_format($selectedConsultation->doctor->consultation_fee, 2) }} @endif
                    </div>
                </div>
                @if(!empty($prefillItems) && count($prefillItems) > 1)
                    <span class="badge badge-light text-primary"><i class="mdi mdi-check"></i> Auto-filled {{ count($prefillItems) }} items</span>
                @endif
            </div>
        </div>
    @endif

    @if(!empty($membership))
        <div class="data-card mb-3" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none">
            <div class="d-flex align-items-center flex-wrap" style="gap:14px">
                <i class="mdi mdi-card-account-details" style="font-size:28px;opacity:0.9"></i>
                <div style="flex:1;min-width:200px">
                    <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">Active Membership</small>
                    <div class="font-weight-bold">{{ $membership->tier->name }} · {{ $membership->membership_number }}</div>
                    <div class="small" style="opacity:0.9">
                        Cons {{ (int) $membership->tier->discount_consultation }}% · Med {{ (int) $membership->tier->discount_medicine }}% · Lab {{ (int) $membership->tier->discount_lab }}% off
                    </div>
                </div>
                <span class="badge badge-light text-success">Auto-applied</span>
            </div>
        </div>
    @endif

    <div x-data="invoiceForm()" x-init="init()">
        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf
            @if(!empty($selectedConsultation))
                <input type="hidden" name="consultation_id" value="{{ $selectedConsultation->id }}">
            @endif

            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- 1. Patient & Appointment --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Patient</h5>
                                <small class="text-muted">Who's being billed?</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-2">
                                <label class="form-label small font-weight-bold">Patient *</label>
                                <select name="patient_id" required class="form-control" x-model="patientId">
                                    <option value="">&mdash; Select patient &mdash;</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatient ?? null) == $patient->id ? 'selected' : '' }}>{{ $patient->patient_id }} &mdash; {{ $patient->name }}</option>
                                    @endforeach
                                </select>
                                @error('patient_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label small font-weight-bold">Linked Appointment</label>
                                <select name="appointment_id" class="form-control">
                                    <option value="">None</option>
                                    @foreach($appointments as $appt)
                                        <option value="{{ $appt->id }}" {{ old('appointment_id', $selectedAppointment) == $appt->id ? 'selected' : '' }}>{{ $appt->appointment_date->format('d/m/Y') }} &mdash; {{ $appt->patient->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Optional &mdash; only completed appointments</small>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Payment type --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-cash-multiple text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Payment Type</h5>
                                <small class="text-muted">Cash or insurance panel?</small>
                            </div>
                        </div>
                        <input type="hidden" name="payment_type" :value="paymentType">
                        <div class="d-flex flex-wrap" style="gap:6px">
                            <button type="button" @click="paymentType = 'cash'"
                                :style="paymentType === 'cash' ? 'background:#10b981;color:#fff;border-color:#10b981' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                style="padding:10px 16px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:140px;text-align:left">
                                <div><i class="mdi mdi-cash"></i> Cash / Self-Pay</div>
                                <small style="opacity:0.85;display:block;font-size:10px;font-weight:500">Patient pays directly</small>
                            </button>
                            <button type="button" @click="paymentType = 'panel'"
                                :style="paymentType === 'panel' ? 'background:#3b82f6;color:#fff;border-color:#3b82f6' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                style="padding:10px 16px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:140px;text-align:left">
                                <div><i class="mdi mdi-shield-check"></i> Panel / Insurance</div>
                                <small style="opacity:0.85;display:block;font-size:10px;font-weight:500">Bill an insurance company</small>
                            </button>
                        </div>

                        <div class="row mt-3" x-show="paymentType === 'panel'" x-cloak>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Insurance Panel *</label>
                                <select name="insurance_panel_id" :required="paymentType === 'panel'" class="form-control">
                                    <option value="">Select Panel</option>
                                    @foreach($insurancePanels as $panel)
                                        <option value="{{ $panel->id }}" {{ old('insurance_panel_id') == $panel->id ? 'selected' : '' }}>{{ $panel->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Patient Insurance</label>
                                <select name="patient_insurance_id" class="form-control">
                                    <option value="">None / Optional</option>
                                    @foreach($patientInsurances as $pi)
                                        <option value="{{ $pi->id }}" {{ old('patient_insurance_id') == $pi->id ? 'selected' : '' }}>{{ $pi->panel->company_name ?? '' }} &mdash; {{ $pi->member_id ?? $pi->policy_number ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Line items --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px">
                            <div class="d-flex align-items-center" style="gap:10px">
                                <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                    <i class="mdi mdi-format-list-bulleted text-white" style="font-size:18px"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold">3. Line Items</h5>
                                    <small class="text-muted">What are you billing for?</small>
                                </div>
                            </div>
                            <span class="badge badge-primary" x-text="items.length + ' items · RM ' + subtotal.toFixed(2)"></span>
                        </div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-2 mb-2" style="background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                                <div class="d-flex align-items-center mb-2" style="gap:8px">
                                    <span class="font-weight-bold" style="color:#6b7280;font-size:12px" x-text="'#' + (index + 1)"></span>
                                    <span class="badge" :class="kindBadge(item.kind)" x-text="kindLabel(item.kind)"></span>
                                    <strong class="ml-auto text-success" x-text="'RM ' + (item.quantity * item.unit_price).toFixed(2)"></strong>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="mdi mdi-close"></i></button>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted">Kind</label>
                                        <select :name="'items['+index+'][kind]'" x-model="item.kind" @change="calcTotal" class="form-control form-control-sm">
                                            <option value="custom">Custom</option>
                                            <option value="consultation">Consultation</option>
                                            <option value="medicine">Medicine</option>
                                            <option value="lab">Lab</option>
                                            <option value="service">Service</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="small text-muted">Service</label>
                                        <select :name="'items['+index+'][service_id]'" @change="prefillService($event, index)" class="form-control form-control-sm">
                                            <option value="">— pick —</option>
                                            @foreach($services as $svc)
                                                <option value="{{ $svc->id }}" data-name="{{ $svc->name }}" data-price="{{ $svc->price }}">{{ $svc->name }} (RM {{ number_format($svc->price, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted">Description *</label>
                                        <input type="text" :name="'items['+index+'][description]'" x-model="item.description" required class="form-control form-control-sm" placeholder="Item description" />
                                    </div>
                                    <div class="col-md-1 mb-2">
                                        <label class="small text-muted">Qty *</label>
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" min="1" required class="form-control form-control-sm" @input="calcTotal" />
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted">Price (RM)</label>
                                        <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model.number="item.unit_price" min="0" required class="form-control form-control-sm" @input="calcTotal" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="d-flex flex-wrap" style="gap:6px">
                            <button type="button" @click="addItem('consultation')" class="btn btn-sm btn-outline-info"><i class="mdi mdi-stethoscope"></i> + Consultation</button>
                            <button type="button" @click="addItem('medicine')" class="btn btn-sm btn-outline-success"><i class="mdi mdi-pill"></i> + Medicine</button>
                            <button type="button" @click="addItem('lab')" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-flask"></i> + Lab</button>
                            <button type="button" @click="addItem('service')" class="btn btn-sm btn-outline-warning"><i class="mdi mdi-medical-bag"></i> + Service</button>
                            <button type="button" @click="addItem('custom')" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-pencil"></i> + Custom</button>
                        </div>
                    </div>

                    {{-- 4. Adjustments --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-percent text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Adjustments</h5>
                                <small class="text-muted">Tax, discount, membership</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Tax (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="tax" x-model.number="tax" value="0" class="form-control" @input="calcTotal" />
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Discount (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="discount" x-model.number="discount" value="0" class="form-control" @input="calcTotal" />
                                </div>
                            </div>
                            @if(!empty($membership))
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small font-weight-bold">Membership Discount</label>
                                    <input type="hidden" name="apply_membership_discount" value="0">
                                    <label class="d-flex align-items-center mb-0 p-2" style="gap:10px;cursor:pointer;background:#dcfce7;border-radius:6px;border:1px solid #bbf7d0">
                                        <input type="checkbox" name="apply_membership_discount" value="1" x-model="applyMembership" @change="calcTotal" style="display:none">
                                        <span :style="applyMembership ? 'background:#10b981' : 'background:#d1d5db'"
                                            style="width:36px;height:20px;border-radius:10px;position:relative;transition:background 0.15s;flex-shrink:0">
                                            <span :style="applyMembership ? 'transform:translateX(16px)' : 'transform:translateX(0)'"
                                                style="position:absolute;top:2px;left:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:transform 0.15s"></span>
                                        </span>
                                        <span style="flex:1;color:#166534">
                                            <span class="font-weight-bold small">Apply tier discount</span>
                                            <small class="d-block" x-show="applyMembership && membershipDiscount > 0" x-cloak>&minus; RM <span x-text="membershipDiscount.toFixed(2)"></span></small>
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 5. Notes --}}
                    <div class="data-card mb-3">
                        <label class="form-label small font-weight-bold"><i class="mdi mdi-note-text-outline"></i> Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Internal notes for this invoice...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold" :disabled="!canSubmit" :style="!canSubmit ? 'opacity:0.5;cursor:not-allowed' : ''"><i class="mdi mdi-receipt"></i> Create Invoice</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live receipt preview --}}
                <div class="col-lg-4">
                    <div style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-receipt-outline"></i> Live Receipt
                        </small>

                        <div class="mt-3 p-4" style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 8px 24px rgba(0,0,0,0.06)">
                            <div class="text-center mb-3 pb-3" style="border-bottom:2px dashed #e5e7eb">
                                <div class="font-weight-bold" style="font-size:11px;letter-spacing:0.15em;color:#6b7280">INVOICE PREVIEW</div>
                                <div class="small text-muted" x-text="paymentType === 'panel' ? 'Panel / Insurance' : 'Cash / Self-Pay'"></div>
                            </div>

                            <div class="small mb-3" x-show="items.some(i => i.description)" x-cloak>
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="d-flex justify-content-between mb-1" x-show="item.description">
                                        <span style="flex:1;min-width:0;padding-right:6px">
                                            <span class="badge" :class="kindBadge(item.kind)" style="font-size:9px" x-text="kindLabel(item.kind)"></span>
                                            <span x-text="item.description"></span>
                                            <span class="text-muted" x-show="item.quantity > 1" x-cloak>×<span x-text="item.quantity"></span></span>
                                        </span>
                                        <strong x-text="'RM ' + (item.quantity * item.unit_price).toFixed(2)"></strong>
                                    </div>
                                </template>
                            </div>

                            <div x-show="!items.some(i => i.description)" x-cloak class="text-muted small text-center py-3" style="font-style:italic">
                                Add items to see them here
                            </div>

                            <hr class="my-2">

                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Subtotal</span>
                                <strong>RM <span x-text="subtotal.toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1" x-show="tax > 0" x-cloak>
                                <span class="text-muted">Tax</span>
                                <strong>+ RM <span x-text="Number(tax).toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1 text-danger" x-show="discount > 0" x-cloak>
                                <span>Discount</span>
                                <strong>&minus; RM <span x-text="Number(discount).toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-1 text-success" x-show="applyMembership && membershipDiscount > 0" x-cloak>
                                <span>Membership</span>
                                <strong>&minus; RM <span x-text="membershipDiscount.toFixed(2)"></span></strong>
                            </div>

                            <div class="text-center mt-3 pt-3" style="border-top:2px dashed #e5e7eb">
                                <div class="font-weight-bold" style="font-size:11px;letter-spacing:0.1em;color:#6b7280">TOTAL DUE</div>
                                <div class="font-weight-bold text-primary" style="font-size:32px;line-height:1">RM <span x-text="grandTotal.toFixed(2)"></span></div>
                            </div>
                        </div>

                        <div class="mt-3 p-2 small" x-show="!canSubmit" x-cloak style="background:#fef3c7;color:#78350f;border-radius:6px">
                            <i class="mdi mdi-information"></i>
                            <span x-text="missingHint"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const KIND_BADGES = { consultation:'badge-info', medicine:'badge-success', lab:'badge-primary', service:'badge-warning', custom:'badge-secondary' };
        const KIND_LABELS = { consultation:'Consult', medicine:'Med', lab:'Lab', service:'Service', custom:'Custom' };

        function invoiceForm() {
            @php
                $defaultItems = [];
                if (!empty($prefillItems)) {
                    foreach ($prefillItems as $item) {
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
                applyMembership: {{ !empty($membership) ? 'true' : 'false' }},
                membershipDiscount: 0,
                subtotal: 0,
                grandTotal: 0,
                tierRates: @json($tierDiscounts),
                paymentType: '{{ old('payment_type', 'cash') }}',
                patientId: '{{ old('patient_id', $selectedPatient ?? '') }}',
                init() { this.calcTotal(); },
                addItem(kind = 'custom') {
                    this.items.push({ description: '', quantity: 1, unit_price: 0, kind });
                },
                removeItem(index) { this.items.splice(index, 1); this.calcTotal(); },
                prefillService(e, index) {
                    const opt = e.target.selectedOptions[0];
                    if (opt && opt.dataset.name) {
                        this.items[index].description = opt.dataset.name;
                        this.items[index].unit_price = parseFloat(opt.dataset.price) || 0;
                        this.items[index].kind = 'service';
                        this.calcTotal();
                    }
                },
                kindBadge(k) { return KIND_BADGES[k] || 'badge-secondary'; },
                kindLabel(k) { return KIND_LABELS[k] || k; },
                calcTotal() {
                    this.subtotal = this.items.reduce((sum, i) => sum + (Number(i.quantity || 0) * Number(i.unit_price || 0)), 0);
                    this.membershipDiscount = 0;
                    if (this.applyMembership) {
                        for (const i of this.items) {
                            const rate = this.tierRates[i.kind] || 0;
                            this.membershipDiscount += (Number(i.quantity || 0) * Number(i.unit_price || 0)) * (rate / 100);
                        }
                    }
                    this.grandTotal = Math.max(0, this.subtotal + Number(this.tax || 0) - Number(this.discount || 0) - this.membershipDiscount);
                },
                get canSubmit() {
                    if (!this.patientId) return false;
                    if (!this.items.some(i => i.description && Number(i.quantity) > 0)) return false;
                    return true;
                },
                get missingHint() {
                    if (!this.patientId) return 'Pick a patient';
                    if (!this.items.some(i => i.description)) return 'Add at least one item with a description';
                    return '';
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
