<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-account-tie mr-2" style="color:#8b5cf6"></i>Edit Locum Doctor</h4>
                <small class="text-muted">{{ $locumDoctor->name }} — {{ $locumDoctor->specialization ?? 'No specialization' }}</small>
            </div>
            <div class="d-flex" style="gap:8px">
                <a href="{{ route('locum-doctors.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left mr-1"></i>Back</a>
                <a href="{{ route('locum-doctors.show', $locumDoctor) }}" class="btn btn-outline-info btn-sm"><i class="mdi mdi-eye mr-1"></i>View</a>
                <a href="{{ route('locum-portal.login') }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="mdi mdi-open-in-new mr-1"></i>Portal</a>
            </div>
        </div>
    </x-slot>

    <div class="row" x-data="locumForm()">
        {{-- Form (left) --}}
        <div class="col-lg-8 mb-3">
            <form method="POST" action="{{ route('locum-doctors.update', $locumDoctor) }}" id="locumForm">
                @csrf @method('PUT')

                {{-- Identity --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(139,92,246,0.12);color:#6d28d9"><i class="mdi mdi-account"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Identity & Contact</h5>
                                <small class="text-muted">Personal details and how to reach them</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-account"></i></span></div>
                                    <input type="text" name="name" value="{{ old('name', $locumDoctor->name) }}" required class="form-control" x-model="form.name" />
                                </div>
                                @error('name') <small class="text-danger"><i class="mdi mdi-alert-circle"></i> {{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>IC Number <small class="text-muted">(used as portal username)</small></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-card-account-details"></i></span></div>
                                    <input type="text" name="ic_number" value="{{ old('ic_number', $locumDoctor->ic_number) }}" class="form-control" placeholder="900101-01-1234" x-model="form.ic" />
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-email"></i></span></div>
                                    <input type="email" name="email" value="{{ old('email', $locumDoctor->email) }}" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-phone"></i></span></div>
                                    <input type="text" name="phone" value="{{ old('phone', $locumDoctor->phone) }}" class="form-control" placeholder="+60 12-345 6789" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Professional --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(16,185,129,0.12);color:#047857"><i class="mdi mdi-stethoscope"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Professional Credentials</h5>
                                <small class="text-muted">Specialization and registration numbers</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Specialization</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-medical-bag"></i></span></div>
                                    <input type="text" name="specialization" value="{{ old('specialization', $locumDoctor->specialization) }}" class="form-control" list="specOptions" placeholder="e.g. General Practice" x-model="form.specialization" />
                                </div>
                                <datalist id="specOptions">
                                    <option value="General Practice">
                                    <option value="Pediatrics">
                                    <option value="Dermatology">
                                    <option value="Cardiology">
                                    <option value="Orthopedics">
                                    <option value="ENT">
                                    <option value="Ophthalmology">
                                    <option value="Internal Medicine">
                                    <option value="Family Medicine">
                                    <option value="Obstetrics & Gynaecology">
                                </datalist>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>MMC Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-card-text"></i></span></div>
                                    <input type="text" name="mmc_number" value="{{ old('mmc_number', $locumDoctor->mmc_number) }}" class="form-control text-uppercase" placeholder="MMC123456" x-model="form.mmc" />
                                </div>
                                <small class="text-muted">Malaysian Medical Council</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>APC Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-certificate"></i></span></div>
                                    <input type="text" name="apc_number" value="{{ old('apc_number', $locumDoctor->apc_number) }}" class="form-control text-uppercase" placeholder="APC2024XXXX" />
                                </div>
                                <small class="text-muted">Annual Practising Certificate</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rates --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(245,158,11,0.12);color:#b45309"><i class="mdi mdi-cash-multiple"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Compensation Rates</h5>
                                <small class="text-muted">Used to calculate session pay</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Hourly Rate</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="hourly_rate" value="{{ old('hourly_rate', $locumDoctor->hourly_rate) }}" class="form-control" placeholder="0.00" x-model="form.hourly" />
                                    <div class="input-group-append"><span class="input-group-text">/ hour</span></div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Session Rate</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="session_rate" value="{{ old('session_rate', $locumDoctor->session_rate) }}" class="form-control" placeholder="0.00" x-model="form.session" />
                                    <div class="input-group-append"><span class="input-group-text">/ session</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 mb-0 small">
                            <i class="mdi mdi-information-outline mr-1"></i>
                            Set whichever applies. When recording a locum session, you'll pick which rate to use.
                        </div>
                    </div>
                </div>

                {{-- Banking --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(6,182,212,0.12);color:#0e7490"><i class="mdi mdi-bank"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Banking Details</h5>
                                <small class="text-muted">For batch payment runs</small>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <textarea name="bank_details" rows="3" class="form-control" placeholder="Bank Name&#10;Account No: 1234567890&#10;Account Name: Doctor Name">{{ old('bank_details', $locumDoctor->bank_details) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Portal Access --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(239,68,68,0.12);color:#b91c1c"><i class="mdi mdi-lock"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Portal Access</h5>
                                <small class="text-muted">Locum logs in at <code>/locum-portal/login</code> with IC + password</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Set / Reset Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-lock"></i></span></div>
                                <input :type="showPwd ? 'text' : 'password'" name="password" class="form-control" autocomplete="new-password" placeholder="••••••••" x-model="newPwd" @input="checkStrength" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" @click="showPwd = !showPwd" tabindex="-1">
                                        <i class="mdi" :class="showPwd ? 'mdi-eye-off' : 'mdi-eye'"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info" @click="generatePassword" tabindex="-1" title="Generate strong password">
                                        <i class="mdi mdi-auto-fix"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2" x-show="newPwd.length > 0">
                                <div class="progress" style="height:5px">
                                    <div class="progress-bar" :class="strengthClass" :style="'width:' + strengthPct + '%'"></div>
                                </div>
                                <small :class="strengthTextClass" x-text="strengthLabel"></small>
                            </div>
                            @error('password') <small class="text-danger d-block mt-1"><i class="mdi mdi-alert-circle"></i> {{ $message }}</small> @enderror
                        </div>

                        @if($locumDoctor->last_login_at)
                            <div class="d-flex align-items-center" style="background:#f0fdf4;border-radius:8px;padding:8px 12px">
                                <i class="mdi mdi-check-circle text-success mr-2"></i>
                                <small>Last logged in <strong>{{ $locumDoctor->last_login_at->diffForHumans() }}</strong></small>
                            </div>
                        @else
                            <div class="d-flex align-items-center" style="background:#fef3c7;border-radius:8px;padding:8px 12px">
                                <i class="mdi mdi-alert text-warning mr-2"></i>
                                <small>Never logged in. Set a password and share it (with IC) so the locum can sign in.</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Active toggle --}}
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Active Locum</strong>
                                <div class="small text-muted">Inactive locums can't log in or be assigned to new sessions.</div>
                            </div>
                            <label class="toggle">
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $locumDoctor->is_active) ? 'checked' : '' }} x-model="form.active" />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('locum-doctors.index') }}" class="btn btn-light"><i class="mdi mdi-close mr-1"></i>Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="mdi mdi-content-save mr-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Live preview (right) --}}
        <div class="col-lg-4 mb-3">
            <div class="card sticky-top" style="top:80px">
                <div class="card-body">
                    <small class="text-muted text-uppercase font-weight-bold" style="letter-spacing:0.05em">Live Preview</small>
                    <div class="locum-preview mt-3 text-center">
                        <div class="locum-preview-avatar" x-text="initials()"></div>
                        <h5 class="mt-3 mb-1 font-weight-bold" x-text="form.name || 'Name'"></h5>
                        <div class="text-muted small mb-2">
                            <i class="mdi mdi-medical-bag"></i> <span x-text="form.specialization || 'No specialization'"></span>
                        </div>
                        <div class="badge mb-2" :class="form.active ? 'badge-success' : 'badge-secondary'">
                            <i class="mdi" :class="form.active ? 'mdi-check-circle' : 'mdi-pause-circle'"></i>
                            <span x-text="form.active ? 'Active' : 'Inactive'"></span>
                        </div>

                        <div class="rate-tiles">
                            <div>
                                <div class="rt-icon"><i class="mdi mdi-clock"></i></div>
                                <div class="rt-num">RM <span x-text="parseFloat(form.hourly || 0).toFixed(2)"></span></div>
                                <div class="rt-lbl">Per Hour</div>
                            </div>
                            <div>
                                <div class="rt-icon"><i class="mdi mdi-calendar-clock"></i></div>
                                <div class="rt-num">RM <span x-text="parseFloat(form.session || 0).toFixed(2)"></span></div>
                                <div class="rt-lbl">Per Session</div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-left small">
                            <div class="text-muted text-uppercase mb-2" style="font-size:0.7rem;letter-spacing:0.05em">Identity</div>
                            <div class="mb-1"><i class="mdi mdi-card-account-details text-info"></i> IC: <strong x-text="form.ic || '—'"></strong></div>
                            <div class="mb-1"><i class="mdi mdi-card-text-outline text-info"></i> MMC: <strong x-text="form.mmc || '—'"></strong></div>

                            <div class="text-muted text-uppercase mb-2 mt-3" style="font-size:0.7rem;letter-spacing:0.05em">Stats</div>
                            <div class="mb-1"><i class="mdi mdi-calendar-check text-success"></i> Total sessions: <strong>{{ $locumDoctor->sessions()->count() }}</strong></div>
                            <div><i class="mdi mdi-cash text-warning"></i> Unpaid: <strong>{{ $locumDoctor->sessions()->where('is_paid', false)->count() }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body py-3">
                    <h6 class="font-weight-bold mb-2"><i class="mdi mdi-key-variant text-info mr-1"></i>Portal Login Tips</h6>
                    <ul class="pl-3 mb-0 small text-muted" style="line-height:1.8">
                        <li>URL: <code>/locum-portal/login</code></li>
                        <li>IC number works as username</li>
                        <li>Use 🪄 button to auto-generate strong password</li>
                        <li>Locum can change it after first login</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-section-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
        }
        .input-group-text { background: #f8fafc; border-color: #e2e8f0; color: #64748b; }

        .toggle { position: relative; display: inline-block; width: 56px; height: 30px; cursor: pointer; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle .slider { position: absolute; inset: 0; background: #cbd5e1; border-radius: 999px; transition: 0.3s; }
        .toggle .slider::before {
            content: ''; position: absolute; width: 24px; height: 24px;
            left: 3px; top: 3px; background: #fff; border-radius: 50%;
            transition: 0.3s; box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .toggle input:checked + .slider { background: linear-gradient(135deg, #8b5cf6, #6366f1); }
        .toggle input:checked + .slider::before { transform: translateX(26px); }

        .locum-preview-avatar {
            width: 84px; height: 84px; border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: #fff; font-weight: 800; font-size: 2rem;
            display: inline-flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
        }
        .rate-tiles { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 16px; }
        .rate-tiles > div {
            background: #f8fafc; border-radius: 10px; padding: 12px;
            text-align: center; border: 1px solid #e2e8f0;
        }
        .rt-icon { color: #8b5cf6; font-size: 1.2rem; margin-bottom: 4px; }
        .rt-num { font-size: 1rem; font-weight: 700; color: #0f172a; }
        .rt-lbl { font-size: 0.65rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function locumForm() {
            return {
                form: {
                    name: @json($locumDoctor->name),
                    specialization: @json($locumDoctor->specialization ?? ''),
                    ic: @json($locumDoctor->ic_number ?? ''),
                    mmc: @json($locumDoctor->mmc_number ?? ''),
                    hourly: @json($locumDoctor->hourly_rate ?? 0),
                    session: @json($locumDoctor->session_rate ?? 0),
                    active: {{ $locumDoctor->is_active ? 'true' : 'false' }},
                },
                showPwd: false,
                newPwd: '',
                strengthPct: 0,
                strengthLabel: '',
                strengthClass: 'bg-danger',
                strengthTextClass: 'text-danger',
                initials() {
                    return (this.form.name || '?').trim().split(/\s+/).slice(0, 2).map(s => s[0]?.toUpperCase()).join('') || '?';
                },
                generatePassword() {
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
                    let pwd = '';
                    for (let i = 0; i < 12; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
                    this.newPwd = pwd;
                    this.showPwd = true;
                    this.checkStrength();
                    if (navigator.clipboard) navigator.clipboard.writeText(pwd);
                    setTimeout(() => alert('Generated password copied to clipboard:\n\n' + pwd + '\n\nShare it with the locum securely. Click Save Changes to apply.'), 100);
                },
                checkStrength() {
                    let p = this.newPwd, score = 0;
                    if (p.length >= 8) score++;
                    if (p.length >= 12) score++;
                    if (/[A-Z]/.test(p)) score++;
                    if (/[0-9]/.test(p)) score++;
                    if (/[^A-Za-z0-9]/.test(p)) score++;
                    this.strengthPct = (score / 5) * 100;
                    if (score <= 1) { this.strengthLabel = 'Very weak'; this.strengthClass = 'bg-danger'; this.strengthTextClass = 'text-danger'; }
                    else if (score === 2) { this.strengthLabel = 'Weak'; this.strengthClass = 'bg-warning'; this.strengthTextClass = 'text-warning'; }
                    else if (score === 3) { this.strengthLabel = 'Fair'; this.strengthClass = 'bg-info'; this.strengthTextClass = 'text-info'; }
                    else if (score === 4) { this.strengthLabel = 'Strong'; this.strengthClass = 'bg-primary'; this.strengthTextClass = 'text-primary'; }
                    else { this.strengthLabel = 'Excellent'; this.strengthClass = 'bg-success'; this.strengthTextClass = 'text-success'; }
                }
            }
        }

        document.getElementById('locumForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving...';
        });
    </script>
</x-app-layout>
