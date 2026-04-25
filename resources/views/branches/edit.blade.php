<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-pencil-box text-primary mr-1"></i>Edit Branch</h4>
                <small class="text-muted">{{ $branch->code }} · {{ $branch->name }}</small>
            </div>
            <div class="d-flex" style="gap:6px">
                <a href="{{ route('branches.show', $branch) }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Branch</a>
                <form method="POST" action="{{ route('branches.destroy', $branch) }}" class="d-inline" onsubmit="return confirm('Delete branch {{ $branch->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete"></i> Delete</button>
                </form>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="branchForm()" x-init="init()">
        <form method="POST" action="{{ route('branches.update', $branch) }}">
            @csrf @method('PUT')
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- 1. Identity --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-tag text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Identity</h5>
                                <small class="text-muted">Branch name and unique code</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-2">
                                <label class="form-label small font-weight-bold">Branch Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" value="{{ old('name', $branch->name) }}" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label small font-weight-bold">Branch Code *</label>
                                <input type="text" name="code" required class="form-control text-uppercase" x-model="code" maxlength="10" value="{{ old('code', $branch->code) }}" style="letter-spacing:0.1em;font-weight:600" />
                                <small class="text-muted">Used in IDs (e.g. CN-<span x-text="code || 'XXX'"></span>-20260425-0001)</small>
                                @error('code')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Contact --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-map-marker text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Address & Contact</h5>
                                <small class="text-muted">How patients reach this branch</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Address</label>
                            <textarea name="address" rows="2" class="form-control" x-model="address" placeholder="Lot 12, Jalan SS15/4, Subang Jaya, 47500 Selangor">{{ old('address', $branch->address) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-phone"></i></span></div>
                                    <input type="text" name="phone" class="form-control" x-model="phone" placeholder="+60 3-1234 5678" value="{{ old('phone', $branch->phone) }}" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-email"></i></span></div>
                                    <input type="email" name="email" class="form-control" x-model="email" placeholder="branch@clinicqo.my" value="{{ old('email', $branch->email) }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Hours --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-clock-outline text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Operating Hours</h5>
                                <small class="text-muted">Default daily hours</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Opening</label>
                                <input type="time" name="opening_time" class="form-control" x-model="opening" value="{{ old('opening_time', $branch->opening_time ? substr($branch->opening_time, 0, 5) : '') }}" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Closing</label>
                                <input type="time" name="closing_time" class="form-control" x-model="closing" value="{{ old('closing_time', $branch->closing_time ? substr($branch->closing_time, 0, 5) : '') }}" />
                            </div>
                        </div>
                        <div class="mt-1 d-flex flex-wrap" style="gap:6px">
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setHours('09:00','17:00')">9-5</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setHours('09:00','18:00')">9-6</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setHours('08:00','20:00')">8-8</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="setHours('00:00','23:59')">24 hours</button>
                        </div>
                    </div>

                    {{-- 4. Status --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-power text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Status</h5>
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="0" />
                        <label class="d-flex align-items-center mb-0 p-3" style="gap:12px;cursor:pointer;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                            <input type="checkbox" name="is_active" value="1" x-model="active" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} style="display:none">
                            <span :style="active ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="active ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold" x-text="active ? 'Active' : 'Inactive'"></span>
                                <small class="d-block text-muted" x-text="active ? 'This branch can take new appointments' : 'No bookings or operations here'"></small>
                            </span>
                        </label>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-content-save"></i> Update Branch</button>
                        <a href="{{ route('branches.show', $branch) }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- Branch card --}}
                        <div class="mt-3 p-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border-radius:10px;position:relative;overflow:hidden">
                            <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <div class="d-flex justify-content-between align-items-start" style="gap:10px">
                                    <div style="flex:1">
                                        <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:600">Branch</small>
                                        <div class="font-weight-bold" style="font-size:18px" x-text="name || 'Branch Name'"></div>
                                    </div>
                                    <span style="background:rgba(255,255,255,0.2);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;letter-spacing:0.1em" x-text="code || 'CODE'"></span>
                                </div>
                                <div class="mt-3 small" x-show="address" x-cloak>
                                    <i class="mdi mdi-map-marker"></i> <span x-text="address"></span>
                                </div>
                                <div class="mt-2 d-flex flex-wrap" style="gap:12px;font-size:13px">
                                    <span x-show="phone" x-cloak><i class="mdi mdi-phone"></i> <span x-text="phone"></span></span>
                                    <span x-show="email" x-cloak><i class="mdi mdi-email"></i> <span x-text="email"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Hours --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a">
                            <small style="color:#92400e;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">
                                <i class="mdi mdi-clock-outline"></i> Operating Hours
                            </small>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="text-center" style="flex:1">
                                    <small class="text-muted">Opens</small>
                                    <div class="font-weight-bold text-success" style="font-size:18px" x-text="opening || '—'"></div>
                                </div>
                                <div style="font-size:24px;color:#9ca3af">&rarr;</div>
                                <div class="text-center" style="flex:1">
                                    <small class="text-muted">Closes</small>
                                    <div class="font-weight-bold text-danger" style="font-size:18px" x-text="closing || '—'"></div>
                                </div>
                            </div>
                            <div class="text-center mt-2" style="font-size:12px;color:#78350f" x-show="hoursLabel" x-cloak>
                                <i class="mdi mdi-timer-sand"></i> <span x-text="hoursLabel"></span> open daily
                            </div>
                        </div>

                        {{-- Status badge --}}
                        <div class="mt-3 p-3 text-center" :style="active ? 'background:#dcfce7;border:1px solid #bbf7d0' : 'background:#fee2e2;border:1px solid #fca5a5'" style="border-radius:10px">
                            <i :class="active ? 'mdi mdi-check-circle text-success' : 'mdi mdi-close-circle text-danger'" style="font-size:24px"></i>
                            <div class="font-weight-bold mt-1" :class="active ? 'text-success' : 'text-danger'" x-text="active ? 'Active' : 'Inactive'"></div>
                        </div>

                        {{-- Change tracker --}}
                        <div class="mt-3 p-2" style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px" x-show="hasChanges" x-cloak>
                            <small style="color:#075985"><i class="mdi mdi-information"></i> <strong>Unsaved changes</strong> — don't forget to save.</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $openingDefault = $branch->opening_time ? substr($branch->opening_time, 0, 5) : '';
        $closingDefault = $branch->closing_time ? substr($branch->closing_time, 0, 5) : '';
    @endphp
    <script>
        function branchForm() {
            return {
                name: @json(old('name', $branch->name)),
                code: @json(old('code', $branch->code)),
                address: @json(old('address', $branch->address)),
                phone: @json(old('phone', $branch->phone)),
                email: @json(old('email', $branch->email)),
                opening: @json(old('opening_time', $openingDefault)),
                closing: @json(old('closing_time', $closingDefault)),
                active: {{ old('is_active', $branch->is_active) ? 'true' : 'false' }},
                original: {},
                init() {
                    this.original = {
                        name: this.name, code: this.code, address: this.address,
                        phone: this.phone, email: this.email, opening: this.opening,
                        closing: this.closing, active: this.active,
                    };
                },
                setHours(o, c) { this.opening = o; this.closing = c; },
                get hoursLabel() {
                    if (!this.opening || !this.closing) return '';
                    const [oh, om] = this.opening.split(':').map(Number);
                    const [ch, cm] = this.closing.split(':').map(Number);
                    let mins = (ch * 60 + cm) - (oh * 60 + om);
                    if (mins < 0) mins += 24 * 60;
                    if (mins >= 23 * 60 + 50) return '24 hours';
                    const h = Math.floor(mins / 60), m = mins % 60;
                    return m === 0 ? `${h} hours` : `${h}h ${m}m`;
                },
                get hasChanges() {
                    if (!this.original.name && !this.name) return false;
                    return this.name !== this.original.name
                        || this.code !== this.original.code
                        || this.address !== this.original.address
                        || this.phone !== this.original.phone
                        || this.email !== this.original.email
                        || this.opening !== this.original.opening
                        || this.closing !== this.original.closing
                        || this.active !== this.original.active;
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
