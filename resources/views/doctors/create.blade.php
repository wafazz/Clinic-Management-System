<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-doctor text-primary mr-1"></i>Add New Doctor</h4>
                <small class="text-muted">Create a doctor account with login credentials</small>
            </div>
            <a href="{{ route('doctors.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Doctors</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="doctorForm()" x-init="init()">
        <form method="POST" action="{{ route('doctors.store') }}">
            @csrf
            <div class="row">
                {{-- LEFT: form sections --}}
                <div class="col-lg-8">

                    {{-- Step 1: Account --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account-key text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Login Account</h5>
                                <small class="text-muted">Used to sign into the staff portal</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Full Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" placeholder="Dr. John Doe" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Email *</label>
                                <input type="email" name="email" required class="form-control" x-model="email" placeholder="doctor@clinic.com" />
                                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Password *</label>
                                <div class="input-group">
                                    <input :type="showPwd ? 'text' : 'password'" name="password" required class="form-control" x-model="password" minlength="8" placeholder="Min 8 characters" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" @click="showPwd = !showPwd"><i :class="showPwd ? 'mdi mdi-eye-off' : 'mdi mdi-eye'"></i></button>
                                        <button type="button" class="btn btn-outline-primary" @click="generatePwd()" title="Generate strong password"><i class="mdi mdi-shuffle-variant"></i></button>
                                    </div>
                                </div>
                                @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                                {{-- Strength meter --}}
                                <div x-show="password" class="mt-1" x-cloak>
                                    <div style="background:#e5e7eb;height:4px;border-radius:2px;overflow:hidden">
                                        <div :style="`background:${strengthColor};width:${strengthPct}%;height:100%;transition:all 0.2s`"></div>
                                    </div>
                                    <small :style="`color:${strengthColor}`" x-text="strengthLabel" class="font-weight-bold"></small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" x-model="phone" placeholder="+60 12-345 6789" value="{{ old('phone') }}" />
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Doctor Info --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-stethoscope text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Professional Info</h5>
                                <small class="text-muted">Specialization, branch, and credentials</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Branch *</label>
                                <select name="branch_id" required class="form-control" x-model="branchId">
                                    <option value="">— Select branch —</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', session('current_branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Specialization</label>
                                <input type="text" name="specialization" list="specList" class="form-control" x-model="specialization" placeholder="General Practice" value="{{ old('specialization') }}" />
                                <datalist id="specList">
                                    <option value="General Practice">
                                    <option value="Pediatrics">
                                    <option value="Cardiology">
                                    <option value="Dermatology">
                                    <option value="ENT">
                                    <option value="Gynecology">
                                    <option value="Orthopedics">
                                    <option value="Internal Medicine">
                                    <option value="Family Medicine">
                                </datalist>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Qualification</label>
                                <input type="text" name="qualification" class="form-control" x-model="qualification" placeholder="MBBS, MD, etc." value="{{ old('qualification') }}" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Consultation Fee (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="consultation_fee" class="form-control" x-model="fee" value="{{ old('consultation_fee', '0') }}" />
                                </div>
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    @foreach([30, 50, 80, 100, 150] as $f)
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="fee = {{ $f }}">RM {{ $f }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">MMC Number</label>
                                <input type="text" name="mmc_number" class="form-control" x-model="mmc" placeholder="MMC12345" value="{{ old('mmc_number') }}" />
                                <small class="text-muted">Malaysian Medical Council registration</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">APC Number</label>
                                <input type="text" name="apc_number" class="form-control" x-model="apc" placeholder="APC67890" value="{{ old('apc_number') }}" />
                                <small class="text-muted">Annual Practising Certificate</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-check-circle"></i> Create Doctor</button>
                        <a href="{{ route('doctors.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- Doctor card --}}
                        <div class="mt-3 p-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border-radius:10px">
                            <div class="d-flex align-items-center" style="gap:12px">
                                <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:22px;border:2px solid rgba(255,255,255,0.3)"
                                     x-text="initials"></div>
                                <div style="flex:1;min-width:0">
                                    <div class="font-weight-bold" style="font-size:16px" x-text="name || 'Dr. ___________'"></div>
                                    <small style="opacity:0.85" x-text="specialization || 'Specialization'"></small>
                                </div>
                            </div>
                            <div class="mt-2 small" style="opacity:0.9" x-show="qualification" x-cloak>
                                <i class="mdi mdi-school"></i> <span x-text="qualification"></span>
                            </div>
                            <div class="mt-2" x-show="fee > 0" x-cloak>
                                <small style="opacity:0.85;text-transform:uppercase;letter-spacing:0.05em">Consultation Fee</small>
                                <div class="font-weight-bold" style="font-size:20px">RM <span x-text="parseFloat(fee || 0).toFixed(2)"></span></div>
                            </div>
                        </div>

                        {{-- Contact --}}
                        <div class="mt-3 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb" x-show="email || phone || branchId" x-cloak>
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Contact & Branch</small>
                            <div class="small mt-2" x-show="email"><i class="mdi mdi-email text-muted"></i> <span x-text="email"></span></div>
                            <div class="small mt-1" x-show="phone"><i class="mdi mdi-phone text-muted"></i> <span x-text="phone"></span></div>
                            <div class="small mt-1" x-show="branchName"><i class="mdi mdi-hospital-building text-muted"></i> <span x-text="branchName"></span></div>
                        </div>

                        {{-- Credentials --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="mmc || apc" x-cloak>
                            <small style="color:#92400e;text-transform:uppercase;letter-spacing:0.05em;font-weight:700">
                                <i class="mdi mdi-shield-check"></i> Credentials
                            </small>
                            <div class="small mt-2" x-show="mmc">MMC: <strong x-text="mmc"></strong></div>
                            <div class="small mt-1" x-show="apc">APC: <strong x-text="apc"></strong></div>
                        </div>

                        {{-- Checklist --}}
                        <div class="mt-3">
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Form Status</small>
                            <div class="mt-2 small">
                                <div :class="name ? 'text-success' : 'text-muted'">
                                    <i :class="name ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Full name
                                </div>
                                <div :class="email ? 'text-success' : 'text-muted'">
                                    <i :class="email ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Email address
                                </div>
                                <div :class="password.length >= 8 ? 'text-success' : 'text-muted'">
                                    <i :class="password.length >= 8 ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Password (8+ chars)
                                </div>
                                <div :class="branchId ? 'text-success' : 'text-muted'">
                                    <i :class="branchId ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Branch assigned
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const BRANCHES = @json($branches->mapWithKeys(fn($b) => [$b->id => $b->name])->all());

        function doctorForm() {
            return {
                name: '{{ old('name') }}',
                email: '{{ old('email') }}',
                password: '',
                phone: '{{ old('phone') }}',
                branchId: '{{ old('branch_id', session('current_branch_id')) }}',
                specialization: '{{ old('specialization') }}',
                qualification: '{{ old('qualification') }}',
                fee: '{{ old('consultation_fee', '0') }}',
                mmc: '{{ old('mmc_number') }}',
                apc: '{{ old('apc_number') }}',
                showPwd: false,
                init() {},
                get initials() {
                    if (!this.name) return 'DR';
                    const parts = this.name.replace(/^Dr\.?\s*/i, '').trim().split(/\s+/);
                    if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                    return (parts[0][0] || 'D').toUpperCase();
                },
                get branchName() {
                    return BRANCHES[this.branchId] || '';
                },
                get strengthScore() {
                    let s = 0;
                    if (this.password.length >= 8) s++;
                    if (this.password.length >= 12) s++;
                    if (/[A-Z]/.test(this.password)) s++;
                    if (/[0-9]/.test(this.password)) s++;
                    if (/[^A-Za-z0-9]/.test(this.password)) s++;
                    return s;
                },
                get strengthPct() {
                    return (this.strengthScore / 5) * 100;
                },
                get strengthColor() {
                    return ['#ef4444','#ef4444','#f59e0b','#f59e0b','#10b981','#059669'][this.strengthScore];
                },
                get strengthLabel() {
                    return ['Very weak','Weak','Fair','Good','Strong','Very strong'][this.strengthScore];
                },
                generatePwd() {
                    const charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
                    let p = '';
                    for (let i = 0; i < 14; i++) p += charset[Math.floor(Math.random() * charset.length)];
                    this.password = p;
                    this.showPwd = true;
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
