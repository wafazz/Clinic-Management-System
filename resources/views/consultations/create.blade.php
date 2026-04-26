<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-stethoscope text-primary mr-1"></i>Start New Consultation</h4>
                <small class="text-muted">Pick a patient and doctor &mdash; the consultation will start immediately</small>
            </div>
            <a href="{{ route('consultations.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Consultations</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @if($patients->isEmpty() || $doctors->isEmpty())
        <div class="data-card mb-3" style="background:#fef3c7;border:1px solid #fde68a">
            <div class="d-flex align-items-center" style="gap:12px">
                <i class="mdi mdi-alert-circle text-warning" style="font-size:28px"></i>
                <div>
                    @if($patients->isEmpty())
                        <strong style="color:#78350f">No active patients found.</strong>
                        <a href="{{ route('patients.create') }}" class="btn btn-sm btn-warning ml-2"><i class="mdi mdi-account-plus"></i> Add Patient</a>
                    @endif
                    @if($doctors->isEmpty())
                        <strong style="color:#78350f">No active doctors found.</strong>
                        <a href="{{ route('doctors.create') }}" class="btn btn-sm btn-warning ml-2"><i class="mdi mdi-doctor"></i> Add Doctor</a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div x-data="consultForm()" x-init="init()">
        <form method="POST" action="{{ route('consultations.start') }}">
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
                                <small class="text-muted">Who is being seen?</small>
                            </div>
                        </div>
                        <select name="patient_id" required class="form-control" x-model="patientId" @change="onPatientChange()">
                            <option value="">&mdash; Select patient &mdash;</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->patient_id }} &mdash; {{ $p->name }}@if($p->phone) &middot; {{ $p->phone }}@endif</option>
                            @endforeach
                        </select>
                        @error('patient_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- 2. Doctor --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-doctor text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Doctor</h5>
                                <small class="text-muted">Who's seeing the patient?</small>
                            </div>
                        </div>
                        <select name="doctor_id" required class="form-control" x-model="doctorId" @change="onDoctorChange()">
                            <option value="">&mdash; Select doctor &mdash;</option>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}">Dr. {{ $d->user->name }} &mdash; {{ $d->specialization ?? 'GP' }} (RM {{ number_format($d->consultation_fee ?? 0, 2) }})</option>
                            @endforeach
                        </select>
                        @error('doctor_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Quick context --}}
                    <div class="data-card mb-3" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <small style="color:#1e40af;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                            <i class="mdi mdi-information"></i> What Happens Next
                        </small>
                        <div class="small mt-2" style="color:#1e3a8a">
                            <div class="mb-1"><i class="mdi mdi-numeric-1-circle"></i> A new consultation record is created</div>
                            <div class="mb-1"><i class="mdi mdi-numeric-2-circle"></i> You're taken to the consultation form to enter vitals, diagnosis, prescription</div>
                            <div><i class="mdi mdi-numeric-3-circle"></i> Once completed, you can issue an invoice</div>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold" :disabled="!canSubmit" :style="!canSubmit ? 'opacity:0.5;cursor:not-allowed' : ''">
                            <i class="mdi mdi-play-circle"></i> Start Consultation
                        </button>
                        <a href="{{ route('consultations.index') }}" class="btn btn-light">Cancel</a>
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
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Patient</small>
                            <div class="d-flex align-items-center mt-2" style="gap:10px">
                                <div :style="`background:${patientGrad}`" style="width:44px;height:44px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700"
                                     x-text="patient.name ? patient.name.charAt(0).toUpperCase() : ''"></div>
                                <div style="flex:1;min-width:0">
                                    <div class="font-weight-bold" x-text="patient.name"></div>
                                    <small class="text-muted">
                                        <span x-text="patient.patient_id"></span>
                                        <span x-show="patient.age">&middot; <span x-text="patient.age"></span> yrs</span>
                                        <span x-show="patient.gender">&middot; <span x-text="patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : ''"></span></span>
                                    </small>
                                </div>
                            </div>
                            <div x-show="patient.phone" class="small mt-2"><i class="mdi mdi-phone text-muted"></i> <span x-text="patient.phone"></span></div>
                            <div x-show="patient.blood_type" class="small mt-1"><i class="mdi mdi-water text-danger"></i> Blood: <strong x-text="patient.blood_type"></strong></div>
                            <div x-show="patient.allergies" class="mt-2 p-2 small" style="background:#fee2e2;color:#991b1b;border-radius:6px;border-left:4px solid #dc2626">
                                <i class="mdi mdi-alert-octagon"></i> <strong>ALLERGIES:</strong> <span x-text="patient.allergies"></span>
                            </div>
                        </div>
                        <div x-show="!patient.name" class="mt-3 p-3 text-center text-muted" style="background:#f8fafc;border-radius:10px;border:1px dashed #e5e7eb">
                            <i class="mdi mdi-account-question" style="font-size:32px;opacity:0.4"></i>
                            <div class="small mt-1">No patient selected</div>
                        </div>

                        {{-- Doctor block --}}
                        <div class="mt-3 p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0" x-show="doctor.name" x-cloak>
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Doctor</small>
                            <div class="d-flex align-items-center mt-2" style="gap:10px">
                                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700"
                                     x-text="doctor.name ? doctor.name.charAt(0).toUpperCase() : ''"></div>
                                <div style="flex:1;min-width:0">
                                    <div class="font-weight-bold">Dr. <span x-text="doctor.name"></span></div>
                                    <small class="text-muted" x-text="doctor.specialization"></small>
                                </div>
                            </div>
                            <div x-show="doctor.mmc" class="small mt-2"><i class="mdi mdi-shield-check text-muted"></i> MMC <span x-text="doctor.mmc"></span></div>
                            <div class="mt-2 font-weight-bold text-success">Fee: RM <span x-text="doctor.fee.toFixed(2)"></span></div>
                        </div>
                        <div x-show="!doctor.name" class="mt-3 p-3 text-center text-muted" style="background:#f8fafc;border-radius:10px;border:1px dashed #e5e7eb">
                            <i class="mdi mdi-doctor" style="font-size:32px;opacity:0.4"></i>
                            <div class="small mt-1">No doctor selected</div>
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

        function consultForm() {
            return {
                patientId: '',
                doctorId: '',
                patient: {},
                doctor: { fee: 0 },
                init() {},
                onPatientChange() { this.patient = PATIENTS[this.patientId] || {}; },
                onDoctorChange() { this.doctor = DOCTORS[this.doctorId] || { fee: 0 }; },
                get patientGrad() {
                    if (this.patient.gender === 'male') return 'linear-gradient(135deg,#1e40af,#1d4ed8)';
                    if (this.patient.gender === 'female') return 'linear-gradient(135deg,#be185d,#9d174d)';
                    return 'linear-gradient(135deg,#475569,#334155)';
                },
                get canSubmit() { return this.patientId && this.doctorId; },
                get missingHint() {
                    if (!this.patientId && !this.doctorId) return 'Pick a patient and a doctor';
                    if (!this.patientId) return 'Pick a patient';
                    return 'Pick a doctor';
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
