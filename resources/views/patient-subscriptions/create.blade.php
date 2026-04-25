<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-package-variant-closed text-primary mr-1"></i>New Subscription</h4>
                <small class="text-muted">Sell a service package to a patient</small>
            </div>
            <a href="{{ route('patient-subscriptions.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Subscriptions</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="subForm()" x-init="init()">
        <form method="POST" action="{{ route('patient-subscriptions.store') }}">
            @csrf
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- 1. Patient --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Patient</h5>
                                <small class="text-muted">Who is buying?</small>
                            </div>
                        </div>
                        <select name="patient_id" required class="form-control" x-model="patientId" @change="onPatientChange()">
                            <option value="">&mdash; Select patient &mdash;</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->patient_id }} &mdash; {{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('patient_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- 2. Package picker --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-package-variant text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Choose a Package</h5>
                                <small class="text-muted">Tap a package card to select</small>
                            </div>
                        </div>
                        <input type="hidden" name="package_id" :value="packageId" required>
                        @if($packages->count())
                            <div class="row">
                                @foreach($packages as $pk)
                                    @php
                                        $pkGrad = $pk->type === 'subscription' ? 'linear-gradient(135deg,#10b981,#059669)'
                                                : ($pk->type === 'bundle' ? 'linear-gradient(135deg,#f59e0b,#d97706)'
                                                : ($pk->price >= 1000 ? 'linear-gradient(135deg,#7c3aed,#5b21b6)'
                                                : 'linear-gradient(135deg,#1e40af,#1e3a8a)'));
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <div @click="selectPackage({{ $pk->id }})"
                                             :style="packageId == {{ $pk->id }} ? 'transform:translateY(-3px);box-shadow:0 12px 24px rgba(59,130,246,0.3);outline:3px solid #3b82f6' : ''"
                                             style="cursor:pointer;background:{{ $pkGrad }};color:#fff;border-radius:12px;padding:18px;transition:all 0.2s;position:relative;overflow:hidden">
                                            <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                                            <div style="position:relative">
                                                <div class="d-flex justify-content-between align-items-start" style="gap:10px">
                                                    <div>
                                                        <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">{{ ucfirst(str_replace('_', '-', $pk->type)) }}</small>
                                                        <h5 class="text-white font-weight-bold mb-1 mt-1">{{ $pk->name }}</h5>
                                                    </div>
                                                    <i class="mdi mdi-check-circle" x-show="packageId == {{ $pk->id }}" x-cloak style="font-size:22px"></i>
                                                </div>
                                                <div class="d-flex align-items-baseline mt-2" style="gap:4px">
                                                    <span style="opacity:0.85">RM</span>
                                                    <span style="font-size:28px;font-weight:700">{{ number_format($pk->price, 0) }}</span>
                                                    <span style="opacity:0.85;font-size:12px">{{ $pk->billing_cycle === 'once' ? '' : '/ ' . str_replace('ly', '', $pk->billing_cycle) }}</span>
                                                </div>
                                                @if($pk->description)
                                                    <div class="small mt-2" style="opacity:0.85">{{ Str::limit($pk->description, 70) }}</div>
                                                @endif
                                                <div class="mt-2 d-flex flex-wrap" style="gap:6px;font-size:11px">
                                                    @if($pk->duration_days)<span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px"><i class="mdi mdi-calendar-range"></i> {{ $pk->duration_days }}d</span>@endif
                                                    @if($pk->max_visits)<span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px"><i class="mdi mdi-check-all"></i> {{ $pk->max_visits }} visits</span>@endif
                                                    @if($pk->allow_partial_payment)<span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px"><i class="mdi mdi-credit-card-wireless"></i> Partial OK</span>@endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-package-variant-closed-remove" style="font-size:42px;opacity:0.4"></i>
                                <p class="mt-2 mb-1">No active packages yet.</p>
                                <a href="{{ route('service-packages.create') }}" class="btn btn-sm btn-primary"><i class="mdi mdi-plus-circle"></i> Create One</a>
                            </div>
                        @endif
                        @error('package_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- 3. Payment --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-cash-multiple text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Payment</h5>
                                <small class="text-muted">How is the patient paying?</small>
                            </div>
                        </div>

                        <label class="form-label small font-weight-bold">Payment Mode *</label>
                        <input type="hidden" name="payment_mode" :value="paymentMode">
                        <div class="d-flex flex-wrap mb-3" style="gap:6px">
                            <button type="button" @click="paymentMode = 'full'"
                                :style="paymentMode === 'full' ? 'background:#10b981;color:#fff;border-color:#10b981' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                style="padding:10px 16px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:140px;text-align:left">
                                <div><i class="mdi mdi-check-decagram"></i> Pay in Full</div>
                                <small style="opacity:0.85;display:block;font-size:10px;font-weight:500">Settle everything today</small>
                            </button>
                            <button type="button" @click="paymentMode = 'partial'" :disabled="package.id && !package.allow_partial_payment"
                                :style="paymentMode === 'partial' ? 'background:#3b82f6;color:#fff;border-color:#3b82f6' : (package.id && !package.allow_partial_payment ? 'background:#f3f4f6;color:#9ca3af;border-color:#e5e7eb;cursor:not-allowed' : 'background:#fff;color:#374151;border-color:#d1d5db')"
                                style="padding:10px 16px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:140px;text-align:left">
                                <div><i class="mdi mdi-credit-card-wireless"></i> Partial Payment</div>
                                <small style="opacity:0.85;display:block;font-size:10px;font-weight:500" x-text="package.id && !package.allow_partial_payment ? 'Not allowed for this package' : 'Deposit now, balance later'"></small>
                            </button>
                        </div>

                        <div class="row" x-show="paymentMode === 'partial'" x-cloak>
                            <div class="col-md-12 mb-2">
                                <label class="form-label small font-weight-bold">Deposit (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="deposit_amount" class="form-control" x-model.number="depositAmount" placeholder="e.g. 200" />
                                </div>
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px" x-show="package.price > 0" x-cloak>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="depositAmount = (package.price * 0.25).toFixed(2)">25%</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="depositAmount = (package.price * 0.5).toFixed(2)">50%</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="depositAmount = (package.price * 0.75).toFixed(2)">75%</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="depositAmount = minDeposit.toFixed(2)">Min</button>
                                </div>
                                <small class="text-danger" x-show="paymentMode === 'partial' && minDeposit > 0 && Number(depositAmount || 0) < minDeposit" x-cloak>
                                    <i class="mdi mdi-alert"></i> Min deposit RM <span x-text="minDeposit.toFixed(2)"></span>
                                </small>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Start Date *</label>
                                <input type="date" name="start_date" required class="form-control" x-model="startDate" value="{{ old('start_date', now()->toDateString()) }}" />
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->toDateString() }}'">Today</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->addDay()->toDateString() }}'">Tomorrow</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->addWeek()->toDateString() }}'">Next week</button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Payment Method *</label>
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                                <div class="d-flex flex-wrap" style="gap:6px">
                                    @foreach([['cash','Cash','mdi-cash','#10b981'],['card','Card','mdi-credit-card-outline','#3b82f6'],['online','Online','mdi-laptop','#8b5cf6']] as $p)
                                        <button type="button" @click="paymentMethod = '{{ $p[0] }}'"
                                            :style="paymentMethod === '{{ $p[0] }}' ? 'background:{{ $p[3] }};color:#fff;border-color:{{ $p[3] }}' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                            style="padding:8px 12px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:90px">
                                            <i class="mdi {{ $p[2] }}"></i> {{ $p[1] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold" :disabled="!canSubmit" :style="!canSubmit ? 'opacity:0.5;cursor:not-allowed' : ''">
                            <i class="mdi mdi-check-circle"></i> Create Subscription
                        </button>
                        <a href="{{ route('patient-subscriptions.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live receipt-style preview --}}
                <div class="col-lg-4">
                    <div style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-receipt-outline"></i> Receipt Preview
                        </small>

                        <div class="mt-3 p-4" style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 8px 24px rgba(0,0,0,0.06);position:relative">
                            <div class="text-center mb-3 pb-3" style="border-bottom:2px dashed #e5e7eb">
                                <div class="font-weight-bold" style="font-size:11px;letter-spacing:0.15em;color:#6b7280">SUBSCRIPTION</div>
                                <h5 class="font-weight-bold mt-1 mb-0" x-text="package.name || 'Package Name'"></h5>
                                <small class="text-muted" x-text="patient.name || 'Patient Name'"></small>
                            </div>

                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Package price</span>
                                <strong>RM <span x-text="Number(package.price || 0).toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Payment mode</span>
                                <strong x-text="paymentMode === 'full' ? 'Full Payment' : 'Partial'"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small" :style="paymentMode === 'partial' ? 'color:#16a34a' : ''">
                                <span class="text-muted">Paying today</span>
                                <strong>RM <span x-text="(paymentMode === 'full' ? Number(package.price || 0) : Number(depositAmount || 0)).toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small" x-show="paymentMode === 'partial'" x-cloak :style="balance > 0 ? 'color:#dc2626' : 'color:#6b7280'">
                                <span class="text-muted">Balance due later</span>
                                <strong>RM <span x-text="balance.toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small" x-show="paymentMode === 'partial' && perSession > 0" x-cloak>
                                <span class="text-muted">~ Per session</span>
                                <strong>RM <span x-text="perSession.toFixed(2)"></span></strong>
                            </div>

                            <hr class="my-2">

                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">Method</span>
                                <strong style="text-transform:capitalize" x-text="paymentMethod"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">Start date</span>
                                <strong x-text="startDateLabel"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small" x-show="endDateLabel" x-cloak>
                                <span class="text-muted">Ends</span>
                                <strong x-text="endDateLabel"></strong>
                            </div>

                            <div class="text-center mt-3 pt-3" style="border-top:2px dashed #e5e7eb">
                                <div class="font-weight-bold" style="font-size:11px;letter-spacing:0.1em;color:#6b7280">TOTAL</div>
                                <div class="font-weight-bold text-primary" style="font-size:28px;line-height:1">RM <span x-text="Number(package.price || 0).toFixed(2)"></span></div>
                            </div>
                        </div>

                        {{-- Required fields hint --}}
                        <div class="mt-3 p-2 small" x-show="!canSubmit" x-cloak style="background:#fef3c7;color:#78350f;border-radius:6px">
                            <i class="mdi mdi-information"></i>
                            <span x-text="missingHint"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $patientMap = $patients->mapWithKeys(fn($p) => [$p->id => ['name' => $p->name, 'id' => $p->patient_id]])->all();
        $packageMap = $packages->mapWithKeys(fn($pk) => [$pk->id => [
            'id' => $pk->id,
            'name' => $pk->name,
            'price' => (float) $pk->price,
            'cycle' => $pk->billing_cycle,
            'duration_days' => (int) ($pk->duration_days ?? 0),
            'max_visits' => (int) ($pk->max_visits ?? 0),
            'allow_partial_payment' => (bool) $pk->allow_partial_payment,
            'min_deposit_amount' => (float) ($pk->min_deposit_amount ?? 0),
            'min_deposit_percent' => (float) ($pk->min_deposit_percent ?? 0),
        ]])->all();
    @endphp

    <script>
        const PATIENTS = @json($patientMap);
        const PACKAGES = @json($packageMap);

        function subForm() {
            return {
                patientId: @json(old('patient_id')),
                packageId: @json(old('package_id')),
                paymentMode: @json(old('payment_mode', 'full')),
                depositAmount: @json(old('deposit_amount', 0)),
                startDate: @json(old('start_date', now()->toDateString())),
                paymentMethod: @json(old('payment_method', 'cash')),
                patient: {},
                package: {},
                init() {
                    this.onPatientChange();
                    this.selectPackage(this.packageId);
                },
                onPatientChange() { this.patient = PATIENTS[this.patientId] || {}; },
                selectPackage(id) {
                    this.packageId = id;
                    this.package = PACKAGES[id] || {};
                    if (this.package.id && !this.package.allow_partial_payment) this.paymentMode = 'full';
                },
                get minDeposit() {
                    if (!this.package.id) return 0;
                    const amt = Number(this.package.min_deposit_amount || 0);
                    const pct = Number(this.package.min_deposit_percent || 0);
                    return Math.max(amt, (Number(this.package.price || 0) * pct) / 100);
                },
                get balance() {
                    if (this.paymentMode === 'full') return 0;
                    return Math.max(0, Number(this.package.price || 0) - Number(this.depositAmount || 0));
                },
                get perSession() {
                    if (!this.package.max_visits || this.balance <= 0) return 0;
                    return this.balance / Number(this.package.max_visits);
                },
                get startDateLabel() {
                    if (!this.startDate) return '—';
                    const d = new Date(this.startDate);
                    return isNaN(d) ? '—' : d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
                },
                get endDateLabel() {
                    if (!this.startDate || !this.package.duration_days) return '';
                    const d = new Date(this.startDate);
                    if (isNaN(d)) return '';
                    d.setDate(d.getDate() + Number(this.package.duration_days));
                    return d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
                },
                get canSubmit() {
                    if (!this.patientId || !this.packageId) return false;
                    if (this.paymentMode === 'partial' && Number(this.depositAmount || 0) < this.minDeposit) return false;
                    return true;
                },
                get missingHint() {
                    if (!this.patientId && !this.packageId) return 'Pick a patient and a package';
                    if (!this.patientId) return 'Pick a patient';
                    if (!this.packageId) return 'Pick a package';
                    if (this.paymentMode === 'partial' && Number(this.depositAmount || 0) < this.minDeposit) return `Deposit must be at least RM ${this.minDeposit.toFixed(2)}`;
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
