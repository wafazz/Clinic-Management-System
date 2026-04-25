<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-card-account-details text-primary mr-1"></i>New Membership</h4>
                <small class="text-muted">Enroll a patient into a tier</small>
            </div>
            <a href="{{ route('patient-memberships.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Memberships</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="memForm()" x-init="init()">
        <form method="POST" action="{{ route('patient-memberships.store') }}">
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
                                <small class="text-muted">Who are you signing up?</small>
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

                    {{-- 2. Tier picker --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-crown text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Choose a Tier</h5>
                                <small class="text-muted">Tap a tier card to select</small>
                            </div>
                        </div>
                        <input type="hidden" name="tier_id" :value="tierId" required>
                        <div class="row">
                            @foreach($tiers as $t)
                                @php
                                    $tierGrad = $t->billing_cycle === 'free' ? 'linear-gradient(135deg,#475569,#334155)'
                                              : ($t->price >= 500 ? 'linear-gradient(135deg,#f59e0b,#d97706)'
                                              : ($t->price >= 100 ? 'linear-gradient(135deg,#7c3aed,#5b21b6)'
                                              : ($t->price >= 50 ? 'linear-gradient(135deg,#1e40af,#1e3a8a)'
                                              : 'linear-gradient(135deg,#0e7490,#155e75)')));
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div @click="selectTier({{ $t->id }})"
                                         :style="tierId == {{ $t->id }} ? 'transform:translateY(-3px);box-shadow:0 12px 24px rgba(59,130,246,0.3);outline:3px solid #3b82f6' : ''"
                                         style="cursor:pointer;background:{{ $tierGrad }};color:#fff;border-radius:12px;padding:18px;transition:all 0.2s;position:relative;overflow:hidden">
                                        <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                                        <div style="position:relative">
                                            <div class="d-flex justify-content-between align-items-start" style="gap:10px">
                                                <div>
                                                    <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">{{ ucfirst($t->billing_cycle) }}</small>
                                                    <h5 class="text-white font-weight-bold mb-1 mt-1">{{ $t->name }}</h5>
                                                </div>
                                                <i class="mdi mdi-check-circle" x-show="tierId == {{ $t->id }}" x-cloak style="font-size:22px"></i>
                                            </div>
                                            <div class="d-flex align-items-baseline mt-2" style="gap:4px">
                                                @if($t->billing_cycle === 'free')
                                                    <span style="font-size:28px;font-weight:700">FREE</span>
                                                @else
                                                    <span style="opacity:0.85">RM</span>
                                                    <span style="font-size:28px;font-weight:700">{{ number_format($t->price, 0) }}</span>
                                                    <span style="opacity:0.85;font-size:12px">/ {{ $t->billing_cycle === 'monthly' ? 'mo' : 'yr' }}</span>
                                                @endif
                                            </div>
                                            @if($t->description)
                                                <div class="small mt-2" style="opacity:0.85">{{ Str::limit($t->description, 60) }}</div>
                                            @endif
                                            <div class="mt-2 d-flex flex-wrap" style="gap:6px;font-size:11px">
                                                @if($t->discount_consultation > 0)<span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px">{{ (int) $t->discount_consultation }}% consult</span>@endif
                                                @if($t->free_consultations_per_year > 0)<span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px">{{ $t->free_consultations_per_year }} free visits</span>@endif
                                                @if($t->priority_queue)<span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px"><i class="mdi mdi-rocket-launch"></i> Priority</span>@endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('tier_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- 3. Dates --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-calendar-range text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Membership Period</h5>
                                <small class="text-muted">End date auto-fills based on the tier's cycle</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Start Date *</label>
                                <input type="date" name="start_date" required class="form-control" x-model="startDate" @change="autoEndDate()" value="{{ old('start_date', now()->toDateString()) }}" />
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->toDateString() }}'; autoEndDate()">Today</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->addDay()->toDateString() }}'; autoEndDate()">Tomorrow</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->startOfMonth()->addMonth()->toDateString() }}'; autoEndDate()">Next month</button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">End Date</label>
                                <input type="date" name="end_date" class="form-control" x-model="endDate" />
                                <small class="text-muted" x-show="periodLabel" x-cloak><i class="mdi mdi-information"></i> <span x-text="periodLabel"></span></small>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Payment --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-cash-multiple text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Payment</h5>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <div class="d-flex flex-wrap" style="gap:6px">
                            @foreach([['cash','Cash','mdi-cash','#10b981'],['card','Card','mdi-credit-card-outline','#3b82f6'],['online','Online','mdi-laptop','#8b5cf6']] as $p)
                                <button type="button" @click="paymentMethod = '{{ $p[0] }}'"
                                    :style="paymentMethod === '{{ $p[0] }}' ? 'background:{{ $p[3] }};color:#fff;border-color:{{ $p[3] }}' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                    style="padding:10px 18px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:120px">
                                    <i class="mdi {{ $p[2] }}"></i> {{ $p[1] }}
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="auto_renew" value="0" />
                        <label class="d-flex align-items-center mb-0 mt-3 p-3" style="gap:12px;cursor:pointer;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                            <input type="checkbox" name="auto_renew" value="1" x-model="autoRenew" {{ old('auto_renew') ? 'checked' : '' }} style="display:none">
                            <span :style="autoRenew ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="autoRenew ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold"><i class="mdi mdi-autorenew text-info"></i> Auto-Renew</span>
                                <small class="d-block text-muted" x-text="autoRenew ? 'Renews automatically when this period ends' : 'Will require manual renewal at period end'"></small>
                            </span>
                        </label>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold" :disabled="!patientId || !tierId" :style="(!patientId || !tierId) ? 'opacity:0.5;cursor:not-allowed' : ''">
                            <i class="mdi mdi-check-circle"></i> Create Membership
                        </button>
                        <a href="{{ route('patient-memberships.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Membership Card Preview
                        </small>

                        {{-- Membership card --}}
                        <div class="mt-3" :style="`background:${cardGrad};color:#fff;border-radius:14px;padding:20px;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.12)`">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <div class="d-flex justify-content-between align-items-center" style="gap:8px">
                                    <small style="opacity:0.85;letter-spacing:0.1em;text-transform:uppercase;font-weight:700"><i class="mdi mdi-card-account-details"></i> Member Card</small>
                                    <span x-show="tier.cycle" x-cloak style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700;letter-spacing:0.05em" x-text="tier.cycle ? tier.cycle.toUpperCase() : ''"></span>
                                </div>
                                <h3 class="text-white font-weight-bold mt-2 mb-0" x-text="tier.name || 'Pick a Tier'"></h3>
                                <div style="opacity:0.9;font-size:13px" x-text="patient.name || 'Patient name'"></div>
                                <small style="opacity:0.7" x-text="patient.id || '&mdash;'"></small>

                                <div class="mt-3 d-flex justify-content-between align-items-end">
                                    <div>
                                        <small style="opacity:0.85;font-size:10px;letter-spacing:0.05em">VALID</small>
                                        <div class="font-weight-bold" style="font-size:13px" x-text="dateRange"></div>
                                    </div>
                                    <div class="text-right" x-show="tier.price > 0" x-cloak>
                                        <small style="opacity:0.85;font-size:10px;letter-spacing:0.05em">FEE</small>
                                        <div class="font-weight-bold" style="font-size:18px">RM <span x-text="Number(tier.price || 0).toFixed(0)"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Benefits --}}
                        <div class="mt-3 p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0" x-show="benefits.length" x-cloak>
                            <small style="color:#166534;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-gift"></i> Benefits Included
                            </small>
                            <ul class="mt-2 mb-0 small" style="padding-left:18px;color:#14532d">
                                <template x-for="b in benefits" :key="b">
                                    <li class="mb-1" x-text="b"></li>
                                </template>
                            </ul>
                        </div>

                        {{-- Auto-renew note --}}
                        <div class="mt-3 p-2 small" :style="autoRenew ? 'background:#dbeafe;border:1px solid #93c5fd;color:#1e3a8a' : 'background:#f3f4f6;border:1px solid #e5e7eb;color:#6b7280'" style="border-radius:6px">
                            <i :class="autoRenew ? 'mdi mdi-autorenew' : 'mdi mdi-clock-alert-outline'"></i>
                            <span x-text="autoRenew ? 'Will auto-renew at period end' : 'Manual renewal required'"></span>
                        </div>

                        {{-- Required fields hint --}}
                        <div class="mt-3 p-2 small" x-show="!patientId || !tierId" x-cloak style="background:#fef3c7;color:#78350f;border-radius:6px">
                            <i class="mdi mdi-information"></i>
                            <span x-text="!patientId && !tierId ? 'Pick a patient and a tier' : (!patientId ? 'Pick a patient' : 'Pick a tier')"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $patientMap = $patients->mapWithKeys(fn($p) => [$p->id => ['name' => $p->name, 'id' => $p->patient_id]])->all();
        $tierMap = $tiers->mapWithKeys(fn($t) => [$t->id => [
            'name' => $t->name,
            'cycle' => $t->billing_cycle,
            'price' => (float) $t->price,
            'discount_consultation' => (float) ($t->discount_consultation ?? 0),
            'discount_medicine' => (float) ($t->discount_medicine ?? 0),
            'discount_lab' => (float) ($t->discount_lab ?? 0),
            'free_consultations' => (int) ($t->free_consultations_per_year ?? 0),
            'free_labs' => (int) ($t->free_lab_tests_per_year ?? 0),
            'max_family' => (int) ($t->max_family_members ?? 0),
            'priority_queue' => (bool) $t->priority_queue,
        ]])->all();
    @endphp

    <script>
        const PATIENTS = @json($patientMap);
        const TIERS = @json($tierMap);

        function memForm() {
            return {
                patientId: @json(old('patient_id')),
                tierId: @json(old('tier_id')),
                startDate: @json(old('start_date', now()->toDateString())),
                endDate: @json(old('end_date')),
                paymentMethod: @json(old('payment_method', 'cash')),
                autoRenew: {{ old('auto_renew') ? 'true' : 'false' }},
                patient: {},
                tier: {},
                init() {
                    this.onPatientChange();
                    this.selectTier(this.tierId, false);
                    if (!this.endDate) this.autoEndDate();
                },
                onPatientChange() { this.patient = PATIENTS[this.patientId] || {}; },
                selectTier(id, recompute = true) {
                    this.tierId = id;
                    this.tier = TIERS[id] || {};
                    if (recompute) this.autoEndDate();
                },
                autoEndDate() {
                    if (!this.startDate || !this.tier.cycle) return;
                    if (this.tier.cycle === 'free') { this.endDate = ''; return; }
                    const d = new Date(this.startDate);
                    if (isNaN(d)) return;
                    if (this.tier.cycle === 'monthly') d.setMonth(d.getMonth() + 1);
                    else if (this.tier.cycle === 'yearly') d.setFullYear(d.getFullYear() + 1);
                    this.endDate = d.toISOString().slice(0, 10);
                },
                get cardGrad() {
                    if (!this.tier.cycle) return 'linear-gradient(135deg,#94a3b8,#64748b)';
                    if (this.tier.cycle === 'free') return 'linear-gradient(135deg,#475569,#334155)';
                    const p = Number(this.tier.price || 0);
                    if (p >= 500) return 'linear-gradient(135deg,#f59e0b,#d97706)';
                    if (p >= 100) return 'linear-gradient(135deg,#7c3aed,#5b21b6)';
                    if (p >= 50) return 'linear-gradient(135deg,#1e40af,#1e3a8a)';
                    return 'linear-gradient(135deg,#0e7490,#155e75)';
                },
                get dateRange() {
                    if (!this.startDate) return '—';
                    const fmt = (s) => {
                        if (!s) return '∞';
                        const d = new Date(s);
                        return isNaN(d) ? '—' : d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
                    };
                    return `${fmt(this.startDate)} → ${fmt(this.endDate)}`;
                },
                get periodLabel() {
                    if (!this.startDate || !this.endDate) return '';
                    const a = new Date(this.startDate), b = new Date(this.endDate);
                    if (isNaN(a) || isNaN(b)) return '';
                    const days = Math.round((b - a) / (1000 * 60 * 60 * 24));
                    if (days < 0) return 'End date is before start';
                    if (days < 31) return `${days} days`;
                    const months = Math.round(days / 30);
                    if (months < 12) return `~${months} months`;
                    return `~${(days / 365).toFixed(1)} years`;
                },
                get benefits() {
                    if (!this.tier.cycle) return [];
                    const out = [];
                    if (this.tier.discount_consultation > 0) out.push(`${this.tier.discount_consultation}% off consultations`);
                    if (this.tier.discount_medicine > 0) out.push(`${this.tier.discount_medicine}% off medicine`);
                    if (this.tier.discount_lab > 0) out.push(`${this.tier.discount_lab}% off lab tests`);
                    if (this.tier.free_consultations > 0) out.push(`${this.tier.free_consultations} free consultations / year`);
                    if (this.tier.free_labs > 0) out.push(`${this.tier.free_labs} free lab tests / year`);
                    if (this.tier.max_family > 0) out.push(`Covers up to ${this.tier.max_family} family members`);
                    if (this.tier.priority_queue) out.push('Priority queue access');
                    return out;
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
