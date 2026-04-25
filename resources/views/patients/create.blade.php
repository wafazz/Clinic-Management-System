<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-account-plus text-primary mr-1"></i>Register Patient</h4>
                <small class="text-muted">Type the IC and most fields will fill themselves</small>
            </div>
            <a href="{{ route('patients.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Patients</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="patientForm()" x-init="init()">
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- 1. Identity --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Identity</h5>
                                <small class="text-muted">Name, IC, branch &mdash; IC autofills DOB &amp; gender</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-2">
                                <label class="form-label small font-weight-bold">Full Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" placeholder="e.g. Ahmad bin Abdullah" value="{{ old('name') }}" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label small font-weight-bold">Branch *</label>
                                <select name="branch_id" required class="form-control" x-model="branchId">
                                    <option value="">&mdash; Select branch &mdash;</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', session('current_branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Patient ID will start with this branch's code</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" x-model="ic" @input="autofillFromIC()" placeholder="900101-14-5678" value="{{ old('ic_number') }}" />
                                <small class="text-muted"><i class="mdi mdi-flash text-warning"></i> Auto-fills DOB and gender from Malaysian IC</small>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label small font-weight-bold">Gender</label>
                                <select name="gender" class="form-control" x-model="gender">
                                    <option value="">&mdash;</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label small font-weight-bold">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" x-model="dob" :max="today" value="{{ old('date_of_birth') }}" />
                                <small class="text-muted" x-show="ageLabel" x-cloak x-text="ageLabel"></small>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Contact --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-phone text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Contact</h5>
                                <small class="text-muted">How to reach the patient</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-phone"></i></span></div>
                                    <input type="text" name="phone" class="form-control" x-model="phone" placeholder="+60 12-345 6789" value="{{ old('phone') }}" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-email"></i></span></div>
                                    <input type="email" name="email" class="form-control" x-model="email" placeholder="patient@email.com" value="{{ old('email') }}" />
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label small font-weight-bold">Address</label>
                                <textarea name="address" rows="2" class="form-control" x-model="address" placeholder="Street, city, postcode">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Emergency Contact --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-phone-alert text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Emergency Contact</h5>
                                <small class="text-muted">Who to call in case of emergency</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Contact Name</label>
                                <input type="text" name="emergency_contact" class="form-control" x-model="emergencyContact" placeholder="Spouse / parent / sibling" value="{{ old('emergency_contact') }}" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Contact Phone</label>
                                <input type="text" name="emergency_phone" class="form-control" x-model="emergencyPhone" placeholder="+60 12-345 6789" value="{{ old('emergency_phone') }}" />
                            </div>
                        </div>
                    </div>

                    {{-- 4. Medical --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#dc2626,#991b1b);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-medical-bag text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Medical Info</h5>
                                <small class="text-muted">Critical for safe treatment &mdash; fill in if known</small>
                            </div>
                        </div>

                        <label class="form-label small font-weight-bold">Blood Type</label>
                        <div class="d-flex flex-wrap mb-3" style="gap:6px">
                            <button type="button" @click="bloodType = ''"
                                :style="bloodType === '' ? 'background:#1e40af;color:#fff;border-color:#1e40af' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                style="padding:6px 14px;border-radius:6px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s">Unknown</button>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <button type="button" @click="bloodType = '{{ $bt }}'"
                                    :style="bloodType === '{{ $bt }}' ? 'background:#dc2626;color:#fff;border-color:#dc2626' : 'background:#fff;color:#dc2626;border-color:#fca5a5'"
                                    style="padding:6px 14px;border-radius:6px;border:2px solid;font-weight:700;font-size:13px;transition:all 0.15s;min-width:54px">{{ $bt }}</button>
                            @endforeach
                        </div>
                        <input type="hidden" name="blood_type" :value="bloodType">

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">
                                    <i class="mdi mdi-alert-octagon text-danger"></i> Allergies
                                </label>
                                <textarea name="allergies" rows="3" class="form-control" x-model="allergies" placeholder="Penicillin, peanuts, latex...">{{ old('allergies') }}</textarea>
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    @foreach(['Penicillin', 'Aspirin', 'Peanuts', 'Latex', 'Shellfish', 'Dust'] as $a)
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:11px" @click="addAllergy('{{ $a }}')">+ {{ $a }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Medical History</label>
                                <textarea name="medical_history" rows="3" class="form-control" x-model="history" placeholder="Past conditions, surgeries, chronic illness...">{{ old('medical_history') }}</textarea>
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    @foreach(['Hypertension', 'Diabetes', 'Asthma', 'Heart Disease'] as $h)
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="addHistory('{{ $h }}')">+ {{ $h }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="data-card mb-3">
                        <input type="hidden" name="is_active" value="0" />
                        <label class="d-flex align-items-center mb-0" style="gap:12px;cursor:pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="active" checked style="display:none">
                            <span :style="active ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="active ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold" x-text="active ? 'Active' : 'Inactive'"></span>
                                <small class="d-block text-muted" x-text="active ? 'Available for booking immediately' : 'Will be hidden from active patient lists'"></small>
                            </span>
                        </label>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-account-plus"></i> Register Patient</button>
                        <a href="{{ route('patients.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- Patient hero --}}
                        <div class="mt-3 p-3" :style="`background:${heroGrad};color:#fff;border-radius:10px;position:relative;overflow:hidden`">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <div class="d-flex align-items-center" style="gap:12px">
                                    <div style="width:54px;height:54px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:22px;border:2px solid rgba(255,255,255,0.3)" x-text="initials"></div>
                                    <div style="flex:1;min-width:0">
                                        <div class="font-weight-bold" style="font-size:16px" x-text="name || 'Patient Name'"></div>
                                        <small style="opacity:0.85" x-text="branchName || 'Pick a branch'"></small>
                                    </div>
                                </div>
                                <div class="mt-2 d-flex flex-wrap" style="gap:8px;font-size:12px">
                                    <span x-show="ageLabel" x-cloak x-text="ageLabel"></span>
                                    <span x-show="gender" x-cloak><i class="mdi" :class="gender === 'male' ? 'mdi-gender-male' : 'mdi-gender-female'"></i> <span x-text="gender ? gender.charAt(0).toUpperCase() + gender.slice(1) : ''"></span></span>
                                    <span x-show="bloodType" x-cloak><i class="mdi mdi-water"></i> <span x-text="bloodType"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Allergy banner --}}
                        <div class="mt-3 p-2" x-show="allergies" x-cloak style="background:#fee2e2;color:#991b1b;border-radius:8px;border-left:4px solid #dc2626">
                            <small class="font-weight-bold"><i class="mdi mdi-alert-octagon"></i> ALLERGIES</small>
                            <div class="small" x-text="allergies"></div>
                        </div>

                        {{-- Contact block --}}
                        <div class="mt-3 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb" x-show="phone || email" x-cloak>
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Contact</small>
                            <div class="small mt-2" x-show="phone"><i class="mdi mdi-phone text-muted"></i> <span x-text="phone"></span></div>
                            <div class="small mt-1" x-show="email" style="word-break:break-all"><i class="mdi mdi-email text-muted"></i> <span x-text="email"></span></div>
                        </div>

                        {{-- Emergency --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="emergencyContact || emergencyPhone" x-cloak>
                            <small style="color:#92400e;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">
                                <i class="mdi mdi-phone-alert"></i> Emergency
                            </small>
                            <div class="font-weight-bold mt-2" x-text="emergencyContact"></div>
                            <div class="small" x-text="emergencyPhone"></div>
                        </div>

                        {{-- Form checklist --}}
                        <div class="mt-3">
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Form Status</small>
                            <div class="mt-2 small">
                                <div :class="name ? 'text-success' : 'text-muted'">
                                    <i :class="name ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Full name
                                </div>
                                <div :class="branchId ? 'text-success' : 'text-muted'">
                                    <i :class="branchId ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Branch assigned
                                </div>
                                <div :class="phone ? 'text-success' : 'text-muted'">
                                    <i :class="phone ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Phone (recommended)
                                </div>
                                <div :class="dob ? 'text-success' : 'text-muted'">
                                    <i :class="dob ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Date of birth
                                </div>
                                <div :class="emergencyPhone ? 'text-success' : 'text-muted'">
                                    <i :class="emergencyPhone ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Emergency phone
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $branchMap = $branches->mapWithKeys(fn($b) => [$b->id => $b->name])->all();
    @endphp

    <script>
        const BRANCHES = @json($branchMap);

        function patientForm() {
            return {
                name: @json(old('name')),
                branchId: @json(old('branch_id', session('current_branch_id'))),
                ic: @json(old('ic_number')),
                gender: @json(old('gender')),
                dob: @json(old('date_of_birth')),
                phone: @json(old('phone')),
                email: @json(old('email')),
                address: @json(old('address')),
                emergencyContact: @json(old('emergency_contact')),
                emergencyPhone: @json(old('emergency_phone')),
                bloodType: @json(old('blood_type', '')),
                allergies: @json(old('allergies')),
                history: @json(old('medical_history')),
                active: true,
                today: '{{ now()->format('Y-m-d') }}',
                init() {},
                get initials() {
                    if (!this.name) return 'P';
                    const parts = this.name.trim().split(/\s+/);
                    if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                    return (parts[0][0] || 'P').toUpperCase();
                },
                get branchName() {
                    return BRANCHES[this.branchId] || '';
                },
                get age() {
                    if (!this.dob) return null;
                    const d = new Date(this.dob);
                    if (isNaN(d)) return null;
                    const diff = Date.now() - d.getTime();
                    const a = new Date(diff).getUTCFullYear() - 1970;
                    return a >= 0 ? a : null;
                },
                get ageLabel() {
                    return this.age !== null ? `${this.age} yrs` : '';
                },
                get heroGrad() {
                    if (this.gender === 'male') return 'linear-gradient(135deg,#1e40af,#1d4ed8)';
                    if (this.gender === 'female') return 'linear-gradient(135deg,#be185d,#9d174d)';
                    return 'linear-gradient(135deg,#475569,#334155)';
                },
                autofillFromIC() {
                    const clean = (this.ic || '').replace(/[^0-9]/g, '');
                    if (clean.length < 6) return;
                    const yy = parseInt(clean.slice(0, 2));
                    const mm = clean.slice(2, 4);
                    const dd = clean.slice(4, 6);
                    const currentYear = new Date().getFullYear() % 100;
                    const fullYear = yy <= currentYear ? 2000 + yy : 1900 + yy;
                    const newDob = `${fullYear}-${mm}-${dd}`;
                    if (!isNaN(new Date(newDob).getTime())) this.dob = newDob;

                    if (clean.length >= 12 && !this.gender) {
                        const lastDigit = parseInt(clean.slice(-1));
                        this.gender = lastDigit % 2 === 0 ? 'female' : 'male';
                    }
                },
                addAllergy(item) {
                    const list = (this.allergies || '').split(',').map(s => s.trim()).filter(Boolean);
                    if (list.includes(item)) return;
                    list.push(item);
                    this.allergies = list.join(', ');
                },
                addHistory(item) {
                    const list = (this.history || '').split(',').map(s => s.trim()).filter(Boolean);
                    if (list.includes(item)) return;
                    list.push(item);
                    this.history = list.join(', ');
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
