<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-account-star text-primary mr-1"></i>New Lead</h4>
                <small class="text-muted">Capture an interested prospect &mdash; convert later</small>
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Leads</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="leadForm()" x-init="init()">
        <form method="POST" action="{{ route('leads.store') }}">
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
                                <h5 class="mb-0 font-weight-bold">1. Lead Info</h5>
                                <small class="text-muted">Name and how to reach them</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Full Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" placeholder="e.g. Siti Aishah" value="{{ old('name') }}" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Phone *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-phone"></i></span></div>
                                    <input type="text" name="phone" required class="form-control" x-model="phone" placeholder="+60 12-345 6789" value="{{ old('phone') }}" />
                                </div>
                                @error('phone')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-email"></i></span></div>
                                    <input type="email" name="email" class="form-control" x-model="email" placeholder="lead@email.com" value="{{ old('email') }}" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">IC Number</label>
                                <input type="text" name="ic_number" class="form-control" x-model="ic" @input="autofillFromIC()" placeholder="900101-14-5678" value="{{ old('ic_number') }}" />
                                <small class="text-muted"><i class="mdi mdi-flash text-warning"></i> Auto-fills DOB &amp; gender</small>
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

                    {{-- 2. Source & Interest --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-bullhorn text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Source &amp; Interest</h5>
                                <small class="text-muted">Where they came from &amp; what they want</small>
                            </div>
                        </div>

                        <label class="form-label small font-weight-bold">Source</label>
                        <input type="hidden" name="source" :value="source">
                        <div class="d-flex flex-wrap mb-3" style="gap:6px">
                            @php
                                $sources = [
                                    ['Facebook', 'mdi-facebook', '#1877f2'],
                                    ['Instagram', 'mdi-instagram', '#e4405f'],
                                    ['TikTok', 'mdi-music', '#000'],
                                    ['Google', 'mdi-google', '#4285f4'],
                                    ['WhatsApp', 'mdi-whatsapp', '#25d366'],
                                    ['Walk-in', 'mdi-walk', '#6b7280'],
                                    ['Referral', 'mdi-account-multiple', '#8b5cf6'],
                                    ['Other', 'mdi-dots-horizontal', '#9ca3af'],
                                ];
                            @endphp
                            @foreach($sources as $s)
                                <button type="button" @click="source = '{{ $s[0] }}'"
                                    :style="source === '{{ $s[0] }}' ? 'background:{{ $s[2] }};color:#fff;border-color:{{ $s[2] }}' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                    style="padding:8px 14px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;display:flex;align-items:center;gap:6px">
                                    <i class="mdi {{ $s[1] }}"></i> {{ $s[0] }}
                                </button>
                            @endforeach
                        </div>
                        <input type="text" class="form-control" placeholder="Or type a custom source" x-model="source" value="{{ old('source') }}" />

                        <div class="row mt-3">
                            <div class="col-md-12 mb-2">
                                <label class="form-label small font-weight-bold">Service Interest</label>
                                <input type="text" name="service_interest" list="serviceList" class="form-control" x-model="service" placeholder="e.g. General check-up, dental cleaning, vaccination" value="{{ old('service_interest') }}" />
                                <datalist id="serviceList">
                                    <option value="General Consultation">
                                    <option value="Health Screening">
                                    <option value="Vaccination">
                                    <option value="Dental Cleaning">
                                    <option value="Pediatric Check-up">
                                    <option value="Pre-employment Medical">
                                    <option value="Wellness Package">
                                </datalist>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Assignment & Notes --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-account-tie text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Assignment &amp; Notes</h5>
                                <small class="text-muted">Who follows up &amp; what they need to know</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Assign To</label>
                            <select name="assigned_to" class="form-control" x-model="assignedTo">
                                <option value="">Unassigned</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" data-name="{{ $u->name }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst(str_replace('_', ' ', $u->role)) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label small font-weight-bold">Notes</label>
                            <textarea name="notes" rows="3" class="form-control" x-model="notes" placeholder="What did the lead say? Any follow-up actions, preferences, or context...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-plus-circle"></i> Create Lead</button>
                        <a href="{{ route('leads.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- Lead card --}}
                        <div class="mt-3 p-3" :style="`background:${heroGrad};color:#fff;border-radius:10px;position:relative;overflow:hidden`">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <span style="background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;letter-spacing:0.1em">NEW LEAD</span>
                                <div class="d-flex align-items-center mt-2" style="gap:12px">
                                    <div style="width:54px;height:54px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:22px;border:2px solid rgba(255,255,255,0.3)" x-text="initials"></div>
                                    <div style="flex:1;min-width:0">
                                        <div class="font-weight-bold" style="font-size:16px" x-text="name || 'Lead Name'"></div>
                                        <small style="opacity:0.85" x-text="phone || 'Phone'"></small>
                                    </div>
                                </div>
                                <div class="mt-2 d-flex flex-wrap" style="gap:8px;font-size:12px">
                                    <span x-show="ageLabel" x-cloak x-text="ageLabel"></span>
                                    <span x-show="gender" x-cloak><i class="mdi" :class="gender === 'male' ? 'mdi-gender-male' : 'mdi-gender-female'"></i> <span x-text="gender ? gender.charAt(0).toUpperCase() + gender.slice(1) : ''"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Source --}}
                        <div class="mt-3 p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0" x-show="source" x-cloak>
                            <small style="color:#166534;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-bullhorn"></i> Source
                            </small>
                            <div class="font-weight-bold mt-1" style="color:#14532d" x-text="source"></div>
                        </div>

                        {{-- Service interest --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="service" x-cloak>
                            <small style="color:#92400e;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-medical-bag"></i> Interested In
                            </small>
                            <div class="font-weight-bold mt-1" style="color:#78350f" x-text="service"></div>
                        </div>

                        {{-- Contact --}}
                        <div class="mt-3 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb" x-show="email || ic" x-cloak>
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">More Info</small>
                            <div class="small mt-2" x-show="email" style="word-break:break-all"><i class="mdi mdi-email text-muted"></i> <span x-text="email"></span></div>
                            <div class="small mt-1" x-show="ic"><i class="mdi mdi-card-account-details text-muted"></i> <span x-text="ic"></span></div>
                        </div>

                        {{-- Assigned --}}
                        <div class="mt-3 p-3" style="background:#eff6ff;border-radius:10px;border:1px solid #bfdbfe" x-show="assignedTo" x-cloak>
                            <small style="color:#1e40af;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-account-tie"></i> Assigned To
                            </small>
                            <div class="font-weight-bold mt-1" style="color:#1e3a8a" x-text="assignedName"></div>
                        </div>

                        {{-- Form checklist --}}
                        <div class="mt-3">
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Form Status</small>
                            <div class="mt-2 small">
                                <div :class="name ? 'text-success' : 'text-muted'">
                                    <i :class="name ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Lead name
                                </div>
                                <div :class="phone ? 'text-success' : 'text-muted'">
                                    <i :class="phone ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Phone (required)
                                </div>
                                <div :class="source ? 'text-success' : 'text-muted'">
                                    <i :class="source ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Source picked
                                </div>
                                <div :class="service ? 'text-success' : 'text-muted'">
                                    <i :class="service ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Service interest
                                </div>
                                <div :class="assignedTo ? 'text-success' : 'text-muted'">
                                    <i :class="assignedTo ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Assigned to staff
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $userMap = $users->mapWithKeys(fn($u) => [$u->id => $u->name])->all();
    @endphp

    <script>
        const USERS = @json($userMap);

        function leadForm() {
            return {
                name: @json(old('name')),
                phone: @json(old('phone')),
                email: @json(old('email')),
                ic: @json(old('ic_number')),
                gender: @json(old('gender')),
                dob: @json(old('date_of_birth')),
                source: @json(old('source')),
                service: @json(old('service_interest')),
                assignedTo: @json(old('assigned_to')),
                notes: @json(old('notes')),
                today: '{{ now()->format('Y-m-d') }}',
                init() {},
                get initials() {
                    if (!this.name) return 'L';
                    const parts = this.name.trim().split(/\s+/);
                    if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                    return (parts[0][0] || 'L').toUpperCase();
                },
                get assignedName() {
                    return USERS[this.assignedTo] || '';
                },
                get age() {
                    if (!this.dob) return null;
                    const d = new Date(this.dob);
                    if (isNaN(d)) return null;
                    return new Date(Date.now() - d.getTime()).getUTCFullYear() - 1970;
                },
                get ageLabel() {
                    return this.age !== null && this.age >= 0 ? `${this.age} yrs` : '';
                },
                get heroGrad() {
                    if (this.gender === 'male') return 'linear-gradient(135deg,#1e40af,#1d4ed8)';
                    if (this.gender === 'female') return 'linear-gradient(135deg,#be185d,#9d174d)';
                    return 'linear-gradient(135deg,#6366f1,#4f46e5)';
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
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
