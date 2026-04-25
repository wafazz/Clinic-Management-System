<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-calendar-edit text-primary mr-1"></i>Edit Appointment #{{ $appointment->id }}</h4>
                <small class="text-muted">{{ $appointment->appointment_date->format('d M Y') }} · {{ substr($appointment->start_time, 0, 5) }}</small>
            </div>
            <div class="d-flex" style="gap:6px">
                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back</a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="apptForm()" x-init="init()">
        <form method="POST" action="{{ route('appointments.update', $appointment) }}">
            @csrf @method('PUT')
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- Patient --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Patient</h5>
                                <small class="text-muted">Who is this appointment for?</small>
                            </div>
                        </div>
                        <select name="patient_id" required class="form-control" x-model="patientId" @change="onPatientChange()">
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>{{ $patient->patient_id }} — {{ $patient->name }}@if($patient->phone) · {{ $patient->phone }}@endif</option>
                            @endforeach
                        </select>
                        @error('patient_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Doctor --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-stethoscope text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Doctor</h5>
                                <small class="text-muted">Schedule shown live</small>
                            </div>
                        </div>
                        <select name="doctor_id" required class="form-control" x-model="doctorId" @change="onDoctorChange()">
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doc->id ? 'selected' : '' }}>Dr. {{ $doc->user->name }} — {{ $doc->specialization ?? 'GP' }} (RM {{ number_format($doc->consultation_fee ?? 0, 2) }})</option>
                            @endforeach
                        </select>
                        @error('doctor_id')<small class="text-danger">{{ $message }}</small>@enderror

                        <div x-show="doctor.schedule && Object.keys(doctor.schedule).length" class="mt-3" x-cloak>
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Weekly Schedule</small>
                            <div class="mt-2 d-flex flex-wrap" style="gap:6px">
                                <template x-for="day in ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']" :key="day">
                                    <div :style="doctor.schedule[day] ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0' : 'background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb'"
                                         style="padding:6px 10px;border-radius:6px;font-size:12px;font-weight:600;min-width:90px;text-align:center">
                                        <span x-text="day.charAt(0).toUpperCase() + day.slice(1,3)"></span>
                                        <div style="font-size:10px;font-weight:500;margin-top:2px" x-text="doctor.schedule[day] || 'Off'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Date & Time --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-clock-outline text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Date &amp; Time</h5>
                                <small class="text-muted">When should they come in?</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Date *</label>
                                <input type="date" name="appointment_date" required class="form-control" x-model="apptDate" />
                                <small class="text-muted" x-text="dayLabel"></small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Start *</label>
                                <input type="time" name="start_time" required class="form-control" x-model="startTime" @change="autoEndTime()" />
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">End *</label>
                                <input type="time" name="end_time" required class="form-control" x-model="endTime" />
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Quick Pick</small>
                            <div class="mt-1 d-flex flex-wrap" style="gap:6px">
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="setPreset('09:00','09:30')">9:00 AM (30m)</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="setPreset('10:00','10:30')">10:00 AM (30m)</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="setPreset('11:00','11:30')">11:00 AM (30m)</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="setPreset('14:00','14:30')">2:00 PM (30m)</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="setPreset('15:00','15:30')">3:00 PM (30m)</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="setPreset(startTime, addMinutes(startTime, 60))">Make 1hr</button>
                            </div>
                        </div>
                    </div>

                    {{-- Reason & Notes --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-note-text-outline text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Reason &amp; Notes</h5>
                                <small class="text-muted">Optional context</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Reason for visit</label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Fever, follow-up, check-up" value="{{ old('reason', $appointment->reason) }}" />
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Notes (internal)</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any internal notes for staff">{{ old('notes', $appointment->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-content-save"></i> Update Appointment</button>
                        <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- Patient block --}}
                        <div class="mt-3 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb" x-show="patient.name" x-cloak>
                            <div class="d-flex align-items-center mb-2" style="gap:10px">
                                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700"
                                     x-text="patient.name ? patient.name.charAt(0).toUpperCase() : ''"></div>
                                <div style="flex:1">
                                    <div class="font-weight-bold" x-text="patient.name"></div>
                                    <small class="text-muted">
                                        <span x-text="patient.patient_id"></span>
                                        <span x-show="patient.age">· <span x-text="patient.age"></span> yrs</span>
                                    </small>
                                </div>
                            </div>
                            <div x-show="patient.phone" class="small mb-1"><i class="mdi mdi-phone text-muted"></i> <span x-text="patient.phone"></span></div>
                            <div x-show="patient.allergies" class="small mt-2 p-2" style="background:#fee2e2;color:#991b1b;border-radius:6px">
                                <i class="mdi mdi-alert"></i> <strong>Allergy:</strong> <span x-text="patient.allergies"></span>
                            </div>
                        </div>

                        {{-- Doctor block --}}
                        <div class="mt-3 p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0" x-show="doctor.name" x-cloak>
                            <small class="text-muted">DOCTOR</small>
                            <div class="font-weight-bold mt-1">Dr. <span x-text="doctor.name"></span></div>
                            <div class="small text-muted" x-text="doctor.specialization"></div>
                            <div class="small mt-1" x-show="doctor.mmc">MMC: <span x-text="doctor.mmc"></span></div>
                            <div class="mt-2 font-weight-bold text-success">Fee: RM <span x-text="doctor.fee.toFixed(2)"></span></div>
                        </div>

                        {{-- When block --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="apptDate && startTime" x-cloak>
                            <small class="text-muted">WHEN</small>
                            <div class="d-flex align-items-center mt-1" style="gap:10px">
                                <div style="background:#fff;border-radius:8px;padding:8px;min-width:60px;text-align:center;border:1px solid #fde68a">
                                    <div class="text-warning font-weight-bold" style="font-size:11px;letter-spacing:0.05em" x-text="dateMonth"></div>
                                    <div class="font-weight-bold" style="font-size:22px;line-height:1" x-text="dateDay"></div>
                                </div>
                                <div>
                                    <div class="font-weight-bold" x-text="dateLong"></div>
                                    <div class="small text-muted">
                                        <i class="mdi mdi-clock-outline"></i>
                                        <span x-text="startTime"></span> – <span x-text="endTime"></span>
                                        <span x-show="duration"> (<span x-text="duration"></span>)</span>
                                    </div>
                                </div>
                            </div>
                            <div x-show="scheduleConflict" class="small mt-2 p-2" style="background:#fee2e2;color:#991b1b;border-radius:6px">
                                <i class="mdi mdi-alert"></i> <strong>Heads up:</strong> Doctor not scheduled on <span x-text="dayName"></span>.
                            </div>
                        </div>

                        {{-- Estimate --}}
                        <div class="mt-3 p-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border-radius:10px" x-show="doctor.fee > 0" x-cloak>
                            <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase">Estimated Cost</small>
                            <div class="font-weight-bold" style="font-size:26px">RM <span x-text="doctor.fee.toFixed(2)"></span></div>
                            <small style="opacity:0.85">Consultation fee · billed after visit</small>
                        </div>

                        {{-- Change tracker --}}
                        <div class="mt-3 p-2" style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px" x-show="hasChanges" x-cloak>
                            <small style="color:#075985"><i class="mdi mdi-information"></i> <strong>Unsaved changes</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const DOCTOR_MAP = @json($doctorMap);
        const PATIENT_MAP = @json($patientMap);

        function apptForm() {
            return {
                patientId: '{{ old('patient_id', $appointment->patient_id) }}',
                doctorId: '{{ old('doctor_id', $appointment->doctor_id) }}',
                apptDate: '{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}',
                startTime: '{{ old('start_time', substr($appointment->start_time, 0, 5)) }}',
                endTime: '{{ old('end_time', substr($appointment->end_time, 0, 5)) }}',
                patient: {},
                doctor: { fee: 0, schedule: {} },
                original: {},
                init() {
                    this.onPatientChange();
                    this.onDoctorChange();
                    this.original = {
                        patientId: this.patientId, doctorId: this.doctorId,
                        apptDate: this.apptDate, startTime: this.startTime, endTime: this.endTime,
                    };
                },
                onPatientChange() { this.patient = PATIENT_MAP[this.patientId] || {}; },
                onDoctorChange() { this.doctor = DOCTOR_MAP[this.doctorId] || { fee: 0, schedule: {} }; },
                autoEndTime() {
                    if (this.startTime && (!this.endTime || this.endTime <= this.startTime)) {
                        this.endTime = this.addMinutes(this.startTime, 30);
                    }
                },
                setPreset(start, end) { this.startTime = start; this.endTime = end; },
                addMinutes(time, mins) {
                    if (!time) return '';
                    const [h, m] = time.split(':').map(Number);
                    const total = h * 60 + m + mins;
                    const nh = String(Math.floor(total / 60) % 24).padStart(2, '0');
                    const nm = String(total % 60).padStart(2, '0');
                    return `${nh}:${nm}`;
                },
                get dayName() {
                    if (!this.apptDate) return '';
                    return new Date(this.apptDate).toLocaleDateString('en-US', { weekday: 'long' });
                },
                get dayLabel() { return this.dayName; },
                get dateMonth() {
                    if (!this.apptDate) return '';
                    return new Date(this.apptDate).toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
                },
                get dateDay() {
                    if (!this.apptDate) return '';
                    return new Date(this.apptDate).getDate();
                },
                get dateLong() {
                    if (!this.apptDate) return '';
                    return new Date(this.apptDate).toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                },
                get duration() {
                    if (!this.startTime || !this.endTime) return '';
                    const [sh, sm] = this.startTime.split(':').map(Number);
                    const [eh, em] = this.endTime.split(':').map(Number);
                    const mins = (eh * 60 + em) - (sh * 60 + sm);
                    if (mins <= 0) return '';
                    if (mins < 60) return `${mins} min`;
                    const h = Math.floor(mins / 60), m = mins % 60;
                    return m === 0 ? `${h} hr` : `${h} hr ${m} min`;
                },
                get scheduleConflict() {
                    if (!this.doctor.schedule || !this.dayName) return false;
                    return !this.doctor.schedule[this.dayName.toLowerCase()];
                },
                get hasChanges() {
                    if (!this.original.patientId) return false;
                    return this.patientId !== this.original.patientId
                        || this.doctorId !== this.original.doctorId
                        || this.apptDate !== this.original.apptDate
                        || this.startTime !== this.original.startTime
                        || this.endTime !== this.original.endTime;
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
