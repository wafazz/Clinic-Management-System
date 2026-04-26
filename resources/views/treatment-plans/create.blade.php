<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-clipboard-list text-primary mr-1"></i>New Treatment Plan</h4>
                <small class="text-muted">Schedule a multi-session course of treatment</small>
            </div>
            <a href="{{ route('treatment-plans.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Plans</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @if($patients->isEmpty() || $doctors->isEmpty())
        <div class="data-card mb-3" style="background:#fef3c7;border:1px solid #fde68a">
            <div class="d-flex align-items-center" style="gap:12px">
                <i class="mdi mdi-alert-circle text-warning" style="font-size:28px"></i>
                <div>
                    @if($patients->isEmpty())<strong style="color:#78350f">No active patients found.</strong> <a href="{{ route('patients.create') }}" class="btn btn-sm btn-warning ml-2"><i class="mdi mdi-account-plus"></i> Add Patient</a>@endif
                    @if($doctors->isEmpty())<strong style="color:#78350f">No active doctors found.</strong> <a href="{{ route('doctors.create') }}" class="btn btn-sm btn-warning ml-2"><i class="mdi mdi-doctor"></i> Add Doctor</a>@endif
                </div>
            </div>
        </div>
    @endif

    <div x-data="planForm()" x-init="init()">
        <form method="POST" action="{{ route('treatment-plans.store') }}">
            @csrf
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- Quick template --}}
                    @if($templates->isNotEmpty())
                        <div class="data-card mb-3" style="background:#f0f9ff;border:1px solid #bae6fd">
                            <small style="color:#075985;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-flash"></i> Start From a Template
                            </small>
                            <input type="hidden" name="template_id" :value="templateId">
                            <div class="mt-2 d-flex flex-wrap" style="gap:6px">
                                <button type="button" @click="applyTemplate('')"
                                    :style="!templateId ? 'background:#0369a1;color:#fff;border-color:#0369a1' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                    style="padding:6px 12px;border-radius:6px;border:2px solid;font-weight:600;font-size:12px;transition:all 0.15s">None</button>
                                @foreach($templates as $t)
                                    <button type="button" @click="applyTemplate('{{ $t->id }}')"
                                        :style="templateId == '{{ $t->id }}' ? 'background:#0369a1;color:#fff;border-color:#0369a1' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                        style="padding:6px 12px;border-radius:6px;border:2px solid;font-weight:600;font-size:12px;transition:all 0.15s">
                                        {{ $t->name }} <small class="text-muted">· {{ $t->total_sessions }}s</small>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 1. Patient & Doctor --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account-multiple text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Who's Involved</h5>
                                <small class="text-muted">Patient and treating doctor</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Patient *</label>
                                <select name="patient_id" required class="form-control" x-model="patientId" @change="onPatientChange()">
                                    <option value="">&mdash; Select patient &mdash;</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}">{{ $p->patient_id }} &mdash; {{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('patient_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Doctor *</label>
                                <select name="doctor_id" required class="form-control" x-model="doctorId" @change="onDoctorChange()">
                                    <option value="">&mdash; Select doctor &mdash;</option>
                                    @foreach($doctors as $d)
                                        <option value="{{ $d->id }}">Dr. {{ $d->user->name }} &mdash; {{ $d->specialization ?? 'GP' }}</option>
                                    @endforeach
                                </select>
                                @error('doctor_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Plan details --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-clipboard-text text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Plan Details</h5>
                                <small class="text-muted">What is being treated?</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Title *</label>
                            <input type="text" name="title" required class="form-control" x-model="title" placeholder="e.g. Physiotherapy for lower back pain" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Diagnosis</label>
                            <input type="text" name="diagnosis" class="form-control" x-model="diagnosis" placeholder="e.g. L5-S1 disc herniation" />
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Description</label>
                            <textarea name="description" rows="2" class="form-control" x-model="description" placeholder="Treatment approach, goals, expectations"></textarea>
                        </div>
                    </div>

                    {{-- 3. Schedule --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-calendar-multiselect text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Schedule</h5>
                                <small class="text-muted">Sessions auto-generated from these values</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Total Sessions *</label>
                                <input type="number" name="total_sessions" required min="1" class="form-control" x-model.number="totalSessions" />
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    @foreach([3, 6, 8, 10, 12] as $s)
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="totalSessions = {{ $s }}">{{ $s }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Interval (days) *</label>
                                <input type="number" name="interval_days" required min="1" class="form-control" x-model.number="intervalDays" />
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="intervalDays = 1">Daily</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="intervalDays = 3">3 days</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="intervalDays = 7">Weekly</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="intervalDays = 14">2 wks</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="intervalDays = 30">Monthly</button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Start Date *</label>
                                <input type="date" name="start_date" required class="form-control" x-model="startDate" :min="today" value="{{ now()->toDateString() }}" />
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->toDateString() }}'">Today</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->addDay()->toDateString() }}'">Tomorrow</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="startDate = '{{ now()->addWeek()->toDateString() }}'">Next week</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Notes --}}
                    <div class="data-card mb-3">
                        <label class="form-label small font-weight-bold"><i class="mdi mdi-note-text-outline"></i> Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Internal notes for staff or other doctors"></textarea>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold" :disabled="!canSubmit" :style="!canSubmit ? 'opacity:0.5;cursor:not-allowed' : ''">
                            <i class="mdi mdi-check-circle"></i> Create Plan
                        </button>
                        <a href="{{ route('treatment-plans.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Plan Preview
                        </small>

                        {{-- Plan card --}}
                        <div class="mt-3 p-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border-radius:10px;position:relative;overflow:hidden">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <small style="opacity:0.85;letter-spacing:0.1em;text-transform:uppercase;font-weight:700"><i class="mdi mdi-clipboard-list"></i> Treatment Plan</small>
                                <h5 class="text-white font-weight-bold mt-2 mb-1" x-text="title || 'Plan Title'"></h5>
                                <div class="small" style="opacity:0.9" x-text="diagnosis || ''"></div>
                            </div>
                        </div>

                        {{-- Patient + doctor --}}
                        <div class="mt-3 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb" x-show="patient.name || doctor.name" x-cloak>
                            <div x-show="patient.name" x-cloak>
                                <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Patient</small>
                                <div class="d-flex align-items-center mt-1" style="gap:8px">
                                    <div :style="patientGrad" style="width:32px;height:32px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px" x-text="patient.name ? patient.name.charAt(0).toUpperCase() : ''"></div>
                                    <div>
                                        <div class="font-weight-bold small" x-text="patient.name"></div>
                                        <small class="text-muted">
                                            <span x-text="patient.patient_id"></span>
                                            <span x-show="patient.age"> &middot; <span x-text="patient.age"></span> yrs</span>
                                        </small>
                                    </div>
                                </div>
                                <div x-show="patient.allergies" class="mt-2 p-1 small" style="background:#fee2e2;color:#991b1b;border-radius:4px">
                                    <i class="mdi mdi-alert-octagon"></i> <span x-text="patient.allergies"></span>
                                </div>
                            </div>
                            <hr class="my-2" x-show="patient.name && doctor.name" x-cloak>
                            <div x-show="doctor.name" x-cloak>
                                <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Doctor</small>
                                <div class="d-flex align-items-center mt-1" style="gap:8px">
                                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px" x-text="doctor.name ? doctor.name.charAt(0).toUpperCase() : ''"></div>
                                    <div>
                                        <div class="font-weight-bold small">Dr. <span x-text="doctor.name"></span></div>
                                        <small class="text-muted" x-text="doctor.specialization"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule summary --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a">
                            <small style="color:#92400e;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-calendar-multiselect"></i> Schedule
                            </small>
                            <div class="mt-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Total sessions</span>
                                    <strong style="color:#78350f" x-text="totalSessions"></strong>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Interval</span>
                                    <strong style="color:#78350f" x-text="intervalLabel"></strong>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">First session</span>
                                    <strong style="color:#78350f" x-text="startDateLabel"></strong>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Expected end</span>
                                    <strong style="color:#78350f" x-text="endDateLabel"></strong>
                                </div>
                                <div class="d-flex justify-content-between small mt-2 pt-2" style="border-top:1px solid #fde68a;color:#78350f;font-weight:700">
                                    <span>Total duration</span>
                                    <span x-text="durationLabel"></span>
                                </div>
                            </div>
                        </div>

                        {{-- First few session dates --}}
                        <div class="mt-3 p-3" style="background:#eff6ff;border-radius:10px;border:1px solid #bfdbfe" x-show="sessionDates.length > 0" x-cloak>
                            <small style="color:#1e40af;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-calendar"></i> Session Schedule
                            </small>
                            <div class="mt-2 small">
                                <template x-for="(d, i) in sessionDates.slice(0, 6)" :key="i">
                                    <div class="d-flex justify-content-between mb-1" style="color:#1e3a8a">
                                        <span><i class="mdi mdi-circle-small"></i> Session <span x-text="i + 1"></span></span>
                                        <span x-text="d"></span>
                                    </div>
                                </template>
                                <div x-show="sessionDates.length > 6" x-cloak class="text-muted small mt-1" style="font-style:italic">
                                    + <span x-text="sessionDates.length - 6"></span> more sessions
                                </div>
                            </div>
                        </div>

                        {{-- Required hint --}}
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
        const PATIENTS = @json($patientMap);
        const DOCTORS = @json($doctorMap);
        const TEMPLATES = @json($templateMap);

        function planForm() {
            return {
                patientId: '',
                doctorId: '',
                templateId: '',
                title: '',
                diagnosis: '',
                description: '',
                totalSessions: 6,
                intervalDays: 7,
                startDate: '{{ now()->toDateString() }}',
                today: '{{ now()->toDateString() }}',
                patient: {},
                doctor: {},
                init() {},
                onPatientChange() { this.patient = PATIENTS[this.patientId] || {}; },
                onDoctorChange() { this.doctor = DOCTORS[this.doctorId] || {}; },
                applyTemplate(id) {
                    this.templateId = id;
                    if (!id) return;
                    const t = TEMPLATES[id];
                    if (!t) return;
                    if (t.total_sessions) this.totalSessions = t.total_sessions;
                    if (t.interval_days) this.intervalDays = t.interval_days;
                    if (t.description && !this.description) this.description = t.description;
                    if (t.name && !this.title) this.title = t.name;
                },
                get patientGrad() {
                    if (this.patient.gender === 'male') return 'background:linear-gradient(135deg,#1e40af,#1d4ed8)';
                    if (this.patient.gender === 'female') return 'background:linear-gradient(135deg,#be185d,#9d174d)';
                    return 'background:linear-gradient(135deg,#475569,#334155)';
                },
                get intervalLabel() {
                    const d = Number(this.intervalDays || 0);
                    if (d === 1) return 'Daily';
                    if (d === 7) return 'Weekly';
                    if (d === 14) return 'Bi-weekly';
                    if (d === 30) return 'Monthly';
                    return `Every ${d} days`;
                },
                get sessionDates() {
                    const out = [];
                    if (!this.startDate) return out;
                    const start = new Date(this.startDate);
                    if (isNaN(start)) return out;
                    const total = Math.max(1, Number(this.totalSessions || 0));
                    const interval = Math.max(1, Number(this.intervalDays || 0));
                    for (let i = 0; i < total; i++) {
                        const d = new Date(start);
                        d.setDate(d.getDate() + (i * interval));
                        out.push(d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }));
                    }
                    return out;
                },
                get startDateLabel() {
                    if (!this.startDate) return '—';
                    const d = new Date(this.startDate);
                    return isNaN(d) ? '—' : d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
                },
                get endDateLabel() {
                    const dates = this.sessionDates;
                    return dates.length ? dates[dates.length - 1] : '—';
                },
                get durationLabel() {
                    if (!this.startDate || !this.totalSessions || !this.intervalDays) return '—';
                    const days = (Number(this.totalSessions) - 1) * Number(this.intervalDays);
                    if (days < 7) return `${days} days`;
                    if (days < 60) return `${Math.round(days / 7)} weeks`;
                    return `~${(days / 30).toFixed(1)} months`;
                },
                get canSubmit() {
                    return this.patientId && this.doctorId && this.title && Number(this.totalSessions) > 0 && Number(this.intervalDays) > 0 && this.startDate;
                },
                get missingHint() {
                    if (!this.patientId) return 'Pick a patient';
                    if (!this.doctorId) return 'Pick a doctor';
                    if (!this.title) return 'Add a plan title';
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
