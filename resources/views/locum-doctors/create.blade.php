<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-account-tie text-primary mr-1"></i>Add Locum Doctor</h4>
                <small class="text-muted">Register a freelance doctor for invitations & payments</small>
            </div>
            <a href="{{ route('locum-doctors.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Locums</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="locumForm()" x-init="init()">
        <form method="POST" action="{{ route('locum-doctors.store') }}">
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
                                <h5 class="mb-0 font-weight-bold">1. Personal Info</h5>
                                <small class="text-muted">Name, contact, identification</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label small font-weight-bold">Full Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" placeholder="Dr. Jane Smith" value="{{ old('name') }}" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Email</label>
                                <input type="email" name="email" class="form-control" x-model="email" placeholder="locum@email.com" value="{{ old('email') }}" />
                                <small class="text-muted">Required for portal login</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" x-model="phone" placeholder="+60 12-345 6789" value="{{ old('phone') }}" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" x-model="ic" placeholder="900101-14-5678" value="{{ old('ic_number') }}" />
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
                        </div>
                    </div>

                    {{-- 2. Credentials --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-shield-check text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Medical Credentials</h5>
                                <small class="text-muted">MMC & APC numbers</small>
                            </div>
                        </div>
                        <div class="row">
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

                    {{-- 3. Rates --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-cash text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Pay Rates</h5>
                                <small class="text-muted">Used to calculate session payouts</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Hourly Rate (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="hourly_rate" class="form-control" x-model="hourly" value="{{ old('hourly_rate', '0') }}" />
                                </div>
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    @foreach([50, 80, 100, 150, 200] as $r)
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="hourly = {{ $r }}">RM {{ $r }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Session Rate (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="session_rate" class="form-control" x-model="session" value="{{ old('session_rate', '0') }}" />
                                </div>
                                <small class="text-muted">If set, overrides hourly &times; hours</small>
                                <div class="mt-1 d-flex flex-wrap" style="gap:4px">
                                    @foreach([200, 400, 600, 800, 1000] as $r)
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="session = {{ $r }}">RM {{ $r }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Banking + Portal Login --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-bank text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Bank & Portal Login</h5>
                                <small class="text-muted">For payouts and locum portal access</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Bank Details</label>
                            <textarea name="bank_details" rows="2" class="form-control" x-model="bank" placeholder="Maybank · 1234 5678 9012 · Jane Smith">{{ old('bank_details') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Portal Password</label>
                            <div class="input-group">
                                <input :type="showPwd ? 'text' : 'password'" name="password" class="form-control" x-model="password" minlength="8" placeholder="Min 8 chars (leave blank to skip portal access)" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" @click="showPwd = !showPwd"><i :class="showPwd ? 'mdi mdi-eye-off' : 'mdi mdi-eye'"></i></button>
                                    <button type="button" class="btn btn-outline-primary" @click="generatePwd()" title="Generate strong password"><i class="mdi mdi-shuffle-variant"></i></button>
                                </div>
                            </div>
                            <small class="text-muted">If set, locum can log in at <code>/locum-portal/login</code> using their email</small>
                            <div x-show="password" class="mt-1" x-cloak>
                                <div style="background:#e5e7eb;height:4px;border-radius:2px;overflow:hidden">
                                    <div :style="`background:${strengthColor};width:${strengthPct}%;height:100%;transition:all 0.2s`"></div>
                                </div>
                                <small :style="`color:${strengthColor}`" x-text="strengthLabel" class="font-weight-bold"></small>
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
                                <small class="d-block text-muted" x-text="active ? 'Available to receive invitations' : 'Cannot be invited'"></small>
                            </span>
                        </label>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-check-circle"></i> Add Locum Doctor</button>
                        <a href="{{ route('locum-doctors.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- ID card --}}
                        <div class="mt-3 p-3" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);color:#fff;border-radius:10px;position:relative;overflow:hidden">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:600">
                                    <i class="mdi mdi-account-tie"></i> Locum Doctor
                                </small>
                                <div class="d-flex align-items-center mt-2" style="gap:12px">
                                    <div style="width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:20px;border:2px solid rgba(255,255,255,0.3)" x-text="initials"></div>
                                    <div style="flex:1;min-width:0">
                                        <div class="font-weight-bold" style="font-size:16px" x-text="name || 'Dr. ___________'"></div>
                                        <small style="opacity:0.85" x-text="specialization || 'Specialization'"></small>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex flex-wrap" style="gap:10px;font-size:12px">
                                    <span x-show="phone" x-cloak><i class="mdi mdi-phone"></i> <span x-text="phone"></span></span>
                                    <span x-show="email" x-cloak><i class="mdi mdi-email"></i> <span x-text="email"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Rate calc --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="hourly > 0 || session > 0" x-cloak>
                            <small style="color:#92400e;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">
                                <i class="mdi mdi-calculator"></i> Pay Estimate
                            </small>
                            <div class="mt-2" x-show="session > 0">
                                <small class="text-muted">Per session</small>
                                <div class="font-weight-bold" style="color:#78350f;font-size:18px">RM <span x-text="parseFloat(session).toFixed(2)"></span></div>
                            </div>
                            <div class="mt-2" x-show="hourly > 0">
                                <small class="text-muted">Hourly fallback</small>
                                <div class="d-flex justify-content-between" style="color:#78350f">
                                    <span class="small">4 hrs</span><strong>RM <span x-text="(hourly * 4).toFixed(2)"></span></strong>
                                </div>
                                <div class="d-flex justify-content-between" style="color:#78350f">
                                    <span class="small">8 hrs</span><strong>RM <span x-text="(hourly * 8).toFixed(2)"></span></strong>
                                </div>
                            </div>
                        </div>

                        {{-- Credentials --}}
                        <div class="mt-3 p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0" x-show="mmc || apc || ic" x-cloak>
                            <small style="color:#166534;text-transform:uppercase;letter-spacing:0.05em;font-weight:700">
                                <i class="mdi mdi-shield-check"></i> Credentials
                            </small>
                            <div class="small mt-2" x-show="ic">IC: <strong x-text="ic"></strong></div>
                            <div class="small mt-1" x-show="mmc">MMC: <strong x-text="mmc"></strong></div>
                            <div class="small mt-1" x-show="apc">APC: <strong x-text="apc"></strong></div>
                        </div>

                        {{-- Portal access --}}
                        <div class="mt-3 p-2" style="border-radius:8px" :style="(email && password.length >= 8) ? 'background:#dbeafe;border:1px solid #93c5fd' : 'background:#fef3c7;border:1px solid #fde68a'">
                            <div class="small" :style="(email && password.length >= 8) ? 'color:#1e40af' : 'color:#92400e'">
                                <i :class="(email && password.length >= 8) ? 'mdi mdi-check-circle' : 'mdi mdi-information'"></i>
                                <span x-text="(email && password.length >= 8) ? 'Portal login enabled' : (!email ? 'No email — portal login disabled' : 'Set password to enable portal')"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function locumForm() {
            return {
                name: '{{ old('name') }}',
                email: '{{ old('email') }}',
                phone: '{{ old('phone') }}',
                ic: '{{ old('ic_number') }}',
                specialization: '{{ old('specialization') }}',
                mmc: '{{ old('mmc_number') }}',
                apc: '{{ old('apc_number') }}',
                hourly: '{{ old('hourly_rate', '0') }}',
                session: '{{ old('session_rate', '0') }}',
                bank: '{{ old('bank_details') }}',
                password: '',
                showPwd: false,
                active: true,
                init() {},
                get initials() {
                    if (!this.name) return 'DR';
                    const parts = this.name.replace(/^Dr\.?\s*/i, '').trim().split(/\s+/);
                    if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                    return (parts[0][0] || 'D').toUpperCase();
                },
                generatePwd() {
                    const charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
                    let p = '';
                    for (let i = 0; i < 14; i++) p += charset[Math.floor(Math.random() * charset.length)];
                    this.password = p;
                    this.showPwd = true;
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
                get strengthPct() { return (this.strengthScore / 5) * 100; },
                get strengthColor() { return ['#ef4444','#ef4444','#f59e0b','#f59e0b','#10b981','#059669'][this.strengthScore]; },
                get strengthLabel() { return ['Very weak','Weak','Fair','Good','Strong','Very strong'][this.strengthScore]; },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
